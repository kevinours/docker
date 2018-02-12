<?php
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use Symfony\Component\Serializer\Serializer;
    use Symfony\Component\Serializer\Encoder\XmlEncoder;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use Symfony\Component\Serializer\Annotation\MaxDepth;

    use forum_culinaire\Domain\Post;
    use forum_culinaire\Domain\Categorie;
    use forum_culinaire\Domain\Topic;
    use forum_culinaire\Domain\User;

    use forum_culinaire\Form\Type\PostType;
    use forum_culinaire\Form\Type\TopicType;
    use forum_culinaire\Form\Type\UserType;
    use forum_culinaire\Form\Type\NewUserType;
    use forum_culinaire\Form\Type\ImageType;
    use forum_culinaire\Form\Type\JsonType;
    use forum_culinaire\Form\Type\XmlType;


    // Home page -> affichage des catégories
    $app->get('/', function () use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $categories = $app['dao.categorie']->findAll();
        $SortDatePosts = $app['dao.post']->SortDatePosts();
        return $app['twig']->render('index.html.twig', array(
            'categories' => $categories,
            'SortDatePosts' => $SortDatePosts,
            'usersOnline' => $UsersOnline
        ));
    })->bind('Accueil');

    // liste des topics de la catégorie choisie
    $app->get('cat/{categorie_id}', function ($categorie_id) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $categorie = $app['dao.categorie']->find($categorie_id);
        $topics = $app['dao.topic']->findAllByCategorie($categorie_id);
        $posts = $app['dao.post']->CountPost();
        return $app['twig']->render('topic.html.twig', array(
            'categorie' => $categorie,
            'topics' => $topics,
            'posts' => $posts,
            'usersOnline' => $UsersOnline
        ));
    })->bind('categorie');

    // Créer un topic
    $app->match('/cat/{categorie_id}/add', function($categorie_id, Request $request) use ($app) {
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $UsersOnline = $app['dao.user']->findAllOnline();
            $categorie = $app['dao.categorie']->find($categorie_id);
            $topic = new Topic();
            $topic->setCategorie($categorie);
            $user = $app['user'];
            $topic->setAuthor($user);
            $TopicForm = $app['form.factory']->create(TopicType::class, $topic);
            $TopicForm->handleRequest($request);
            
            if ($TopicForm->isSubmitted() && $TopicForm->isValid()) {
                $id =  $app['dao.topic']->save($topic);
                $app['session']->getFlashBag()->add('success', 'L\'article a bien été créé.');
                $PostVar= array('categorie_id' => $categorie_id, 'top_id' => $id);
                return $app->redirect($app['url_generator']->generate('post', $PostVar));
            }
        }
        return $app['twig']->render('topic_form.html.twig', array(
            'title' => 'Nouveau sujet',
            'topicForm' => $TopicForm->createView(),        
            'usersOnline' => $UsersOnline
        ));
    })->bind('topic_add');

    // liste des post du topic choisi
    $app->match('/cat/{categorie_id}/top/{top_id}', function ($categorie_id, $top_id, Request $request) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $categorie = $app['dao.categorie']->find($categorie_id);
        $topic = $app['dao.topic']->find($top_id);
        $postFormView = null;

        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            $post = new Post();
            $post->setTopic($topic);
            $user = $app['user'];
            $post->setAuthor($user);
            $postForm = $app['form.factory']->create(PostType::class, $post);
            $postForm->handleRequest($request);
            
            if ($postForm->isSubmitted() && $postForm->isValid()) {
                $app['dao.post']->save($post);
                $app['session']->getFlashBag()->add('success', 'Votre réponse a bien été postée.');
            }
            $postFormView = $postForm->createView();
        }
        $posts = $app['dao.post']->findAllByTopic($top_id);
        return $app['twig']->render('post.html.twig', array(
            'categorie' => $categorie,
            'topic' => $topic, 
            'posts' => $posts,
            'postForm' => $postFormView,
            'usersOnline' => $UsersOnline
        ));
    })->bind('post');;

    // Login form
    $app->get('/login', function(Request $request) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();   
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            'usersOnline' => $UsersOnline
        ));
    })->bind('connexion');

    // signup form
    $app->match('/signup', function(Request $request) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $user = new User();
        $userForm = $app['form.factory']->create(NewUserType::class, $user);
        $userForm->handleRequest($request);
        
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
            // find the default encoder
            $encoder = $app['security.encoder.bcrypt'];
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Compte crée, vous pouvez vous connecter.');
            return $app->redirect($app['url_generator']->generate('Accueil'));
        }
        return $app['twig']->render('signup.html.twig', array(
            'title' => 'New user',
            'userForm' => $userForm->createView(),
            'usersOnline' => $UsersOnline
        ));
    })->bind('inscription');

    // admin
$app->match('/admin', function(Request $request) use ($app) {
if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
    $UsersOnline = $app['dao.user']->findAllOnline();

    $JsonForm = $app['form.factory']->create(JsonType::class);
    $JsonForm->handleRequest($request);
    
    if ($JsonForm->isSubmitted() && $JsonForm->isValid()) {
            $json = $JsonForm['choixCat']->getData();
            $categorie = $app['dao.categorie']->find($json);
            $topics = $app['dao.topic']->findAllByCategorie($json);
        
    // Convert an object ($article) into an associative array ($responseData)
     $responseData[] = array(
         'categorie' => $categorie->getName());
        
    foreach ($topics as $topic) {
        $responseData[] = array(
            'id' => $topic->getId(),
            'title' => $topic->getTitle()
        );
        
        $posts = $app['dao.post']->findAllByTopic($topic->getId());
        
        foreach ($posts as $post) {    
            $responseData[] = array(
                'idPost' => $post->getId(),
                'author' => $post->getAuthor()->getUsername(),
                'content' => $post->getContent()
            );
        };
    }
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
    file_put_contents("JSON_categorie".$json.".json", $serializer->serialize($responseData, 'json'));
            return $app->redirect($app['url_generator']->generate('admin'));
    }
    
        
    $XmlForm = $app['form.factory']->create(XmlType::class);
    $XmlForm->handleRequest($request);
    
    if ($XmlForm->isSubmitted() && $XmlForm->isValid()) {
        
            $numCat = $XmlForm['choixCat']->getData();
        
           $bdd = new PDO('mysql:host=localhost;dbname=forum_culinaire;charset=utf8', 'root', '');


            $sql="select *
                from t_post 
                natural join t_user               
                natural join t_topic
                where cat_id=".$numCat;
   
            $data=$bdd->query($sql);

            $xml= new XMLWriter();
            $xml->openUri("XML_categorie".$numCat.".xml");
            $xml->startDocument('1.0', 'utf-8');
            $xml->startElement('categorie');
            $xml->writeAttribute('id', $numCat);

              while($post=$data->fetch()){
                $xml->startElement('topic');     
                $xml->writeAttribute('id', $post['top_id']);
                $xml->startElement('post');
                $xml->writeAttribute('id', $post['post_id']);
                $xml->writeElement('author', $post['usr_name']);
                $xml->writeElement('content',$post['post_content']);
                $xml->endElement();
              }
            $xml->endElement();
            $xml->endElement();
            $xml->flush();;


    }
    $users = $app['dao.user']->findAll();
    return $app['twig']->render('admin.html.twig', array(
        'users' => $users,
        'JsonForm' => $JsonForm->createView(),        
        'XmlForm' => $XmlForm->createView(),
        'usersOnline' => $UsersOnline)
        );
}   
else 
    return $app->redirect($app['url_generator']->generate('login')); 
})->bind('admin');

    // Add a user
    $app->match('/admin/user/add', function(Request $request) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $user = new User();
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
            // find the default encoder
            $encoder = $app['security.encoder.bcrypt'];
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été créé.');
            return $app->redirect($app['url_generator']->generate('admin')); 
        }
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'New user',
            'userForm' => $userForm->createView(),
            'usersOnline' => $UsersOnline
        ));
    })->bind('admin_user_add');

    // Edit an existing user
    $app->match('/admin/user/{id}/edit', function($id, Request $request) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $user = $app['dao.user']->find($id);
        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password); 
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'L\'utilisateur a bien été mis à jour.');
            return $app->redirect($app['url_generator']->generate('admin'));
        }
        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Edit user',
            'userForm' => $userForm->createView(),
            'usersOnline' => $UsersOnline
        ));
    })->bind('admin_user_edit');

    // Remove a user
    $app->get('/admin/user/{id}/delete', function($id, Request $request) use ($app) {
        // mise a jour de l'usr_id
        $app['dao.post']->changeAllByUser($id);
        $app['dao.topic']->changeAllByUser($id);
        // Delete the user
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('succes', 'L\'utilisateur a correctement été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    })->bind('admin_user_delete');

    // Edit an existing post
    $app->match('/admin/comment/{id}/edit', function($id, Request $request) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $comment = $app['dao.comment']->find($id);
        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Le commentaire a bien été mis à jour.');
        }
        return $app['twig']->render('comment_form.html.twig', array(
            'title' => 'Edit comment',
            'commentForm' => $commentForm->createView(),        
            'usersOnline' => $UsersOnline
        ));
    })->bind('admin_comment_edit');

    // Remove a post
    $app->get('/cat/{categorie_id}/top/{top_id}/post/{id}/delete', function($categorie_id, $top_id, $id, Request $request) use ($app) {
        $app['dao.post']->delete($id);
        $app['session']->getFlashBag()->add('success', 'Le post a bien été supprimé.');
        $PostVar= array('categorie_id' => $categorie_id, 'top_id' => $top_id);
        return $app->redirect($app['url_generator']->generate('post', $PostVar));
    })->bind('admin_post_delete');

    // Remove a topic
    $app->get('/cat/{categorie_id}/top/{top_id}/delete', function($categorie_id, $top_id, Request $request) use ($app) {
        $app['dao.topic']->delete($top_id);
        //    $app['session']->getFlashBag()->add('success', 'Le post a bien été supprimé.');
        $PostVar= array('categorie_id' => $categorie_id);
        return $app->redirect($app['url_generator']->generate('categorie', $PostVar));
    })->bind('admin_topic_delete');

    // affichage profil et gestion upload image
    $app->match('/profil', function (Request $request) use ($app) {
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $UsersOnline = $app['dao.user']->findAllOnline();
            $id=$_SESSION['id'];
            $user = $app['dao.user']->find($id);
            $ImageForm = $app['form.factory']->create(ImageType::class, $user);
            $ImageForm->handleRequest($request);

            if ($ImageForm->isSubmitted() && $ImageForm->isValid()) {
                $file = $request->files->get('image');

                if ($file['file'] !== null) {
                    $taille_fichier = filesize($file['file']);
                    $taille_max    = 500000;
                    $extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );
                    //1. strrchr renvoie l'extension avec le point (« . »).
                    //2. substr(chaine,1) ignore le premier caractère de chaine.
                    //3. strtolower met l'extension en minuscules.
                    $extension_upload = strtolower(  substr(  strrchr($file['file']->getClientOriginalName(), '.')  ,1)  );
                    if ( !in_array($extension_upload,$extensions_valides) ) {
                        $app['session']->getFlashBag()->add('danger', 'Le format n\'est pas bon.');
                        return $app->redirect($app['url_generator']->generate('profil')); 
                    }

                    if ($taille_fichier > $taille_max){
                        $app['session']->getFlashBag()->add('danger', 'L\'image est trop volumineuse.');
                        return $app->redirect($app['url_generator']->generate('profil')); 
                    }
                    $pictureToDelete = $app['dao.user']->pictureToDelete($id);

                    if($pictureToDelete !== null){
                        $ouverture = opendir ("img/avatar"); 
                        unlink ("img/avatar/".$pictureToDelete); 
                        closedir ($ouverture); 
                    }
                    $path = 'img/avatar/';
                    $file['file']->move($path, $id."_".$file['file']->getClientOriginalName());

                    $id=$_SESSION['id'];
                    $nameImage= $id."_".$file['file']->getClientOriginalName();
                    $app['session']->getFlashBag()->add('success', 'Votre avatar a bien été changé.');  
                    $app['dao.user']->savePicture($nameImage, $id);              
                    return $app->redirect($app['url_generator']->generate('profil')); 
                }
            }
        }
        else 
            return $app->redirect($app['url_generator']->generate('login')); 

        return $app['twig']->render('profil.html.twig', array(
            'user' => $user,
            'imageForm' => $ImageForm->createView(),
            'usersOnline' => $UsersOnline
        ));
    })->bind('profil');

    $app->get('/pre_login', function() use ($app) {
        if(isset($_SESSION['id'])) {
            $id=$_SESSION['id'];
            $app['dao.user']->isOnline($id);
        }
        return $app->redirect($app['url_generator']->generate('Accueil'));
    })->bind('pre_login');

    $app->get('/pre_logout', function() use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        $id=$_SESSION['id'];
        $app['dao.user']->isOffline($id);
        return $app->redirect($app['url_generator']->generate('logout'));
    })->bind('pre_logout');

    $app->match ('/search', function() use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();

        if(isset($_POST['searchByTopic']) && $_POST['searchByTopic'] != NULL){
            $content = $_POST['searchByTopic'];
            $topics = $app['dao.topic']->search($content);

            if (empty($topics)) {    
                return $app['twig']->render('search.html.twig', array(
                    'usersOnline' => $UsersOnline,
                    'content' => $content  
                ));
            }
            return $app['twig']->render('search.html.twig', array(
                'topics' => $topics,
                'usersOnline' => $UsersOnline,
                'content' => $content 
            ));
        }

        return $app['twig']->render('search.html.twig', array(
            'usersOnline' => $UsersOnline
        ));
    })->bind('recherche');

    // 404 - Page not found
    $app->error(function (\Exception $e) use ($app) {
        $UsersOnline = $app['dao.user']->findAllOnline();
        
        if ($e instanceof NotFoundHttpException) {
            return $app['twig']->render('error.html.twig', array(
                'code' => '404',
                'usersOnline' => $UsersOnline
            ));
        }

        $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;
        return $app['twig']->render('error.html.twig', array(
            'code' => $code,
            'usersOnline' => $UsersOnline
        ));
    });