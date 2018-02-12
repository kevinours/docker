<?php
    use Symfony\Component\Debug\ErrorHandler;
    use Symfony\Component\Debug\ExceptionHandler;

    // Register global error and exception handlers
    ErrorHandler::register();
    ExceptionHandler::register();

    // Register service providers
    $app->register(new Silex\Provider\DoctrineServiceProvider());
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views',
    ));

    $app->register(new Silex\Provider\AssetServiceProvider(), array(
        'assets.version' => 'v1'
    ));

    $app['twig'] = $app->extend('twig', function(Twig_Environment $twig, $app) {
        $twig->addExtension(new Twig_Extensions_Extension_Text());
        return $twig;
    });

    $app->register(new Silex\Provider\ValidatorServiceProvider());
    $app->register(new Silex\Provider\SessionServiceProvider());
    $app->register(new Silex\Provider\SecurityServiceProvider(), array(
        'security.firewalls' => array(
            'secured' => array(
                'pattern' => '^/',
                'anonymous' => true,     
                'logout' => true,
                'form' => array('login_path' => '/login', 'check_path' => '/login_check', 'default_target_path' => '/pre_login'),
                'users' => function () use ($app) {    
                    return new forum_culinaire\DAO\UserDAO($app['db']);
                },
            ),
        ),
    ));
    $app->register(new Silex\Provider\FormServiceProvider());
    $app->register(new Silex\Provider\LocaleServiceProvider());
    $app->register(new Silex\Provider\TranslationServiceProvider());

    // Register services
    $app['dao.categorie'] = function ($app) {
        return new forum_culinaire\DAO\CategorieDAO($app['db']);
    };
    $app['dao.topic'] = function ($app) {
        $topicDAO = new forum_culinaire\DAO\TopicDAO($app['db']);
        $topicDAO->setCategorieDAO($app['dao.categorie']);
        return $topicDAO;
    };

    $app['dao.post'] = function ($app) {
        $postDAO = new forum_culinaire\DAO\PostDAO($app['db']);
        $postDAO->setTopicDAO($app['dao.topic']);
        $postDAO->setUserDAO($app['dao.user']);
        return $postDAO;
    };

    $app['dao.user'] = function ($app) {
        return new forum_culinaire\DAO\UserDAO($app['db']);
    };