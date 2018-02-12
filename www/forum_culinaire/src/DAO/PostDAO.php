<?php
    namespace forum_culinaire\DAO;

    use forum_culinaire\Domain\Post;
    class PostDAO extends DAO {
        /**
        * @var \MicroCMS\DAO\ArticleDAO
        */
        private $topicDAO;
        private $userDAO;

        public function setTopicDAO(TopicDAO $topicDAO) {
            $this->topicDAO = $topicDAO;
        }

        public function setUserDAO(UserDAO $userDAO) {
            $this->userDAO = $userDAO;
        }

        public function save(Post $post) {
            $now=date("Y-m-j H:i");
            $postData = array(
                'top_id' => $post->getTopic()->getId(),
                'usr_id' => $post->getAuthor()->getId(),
                'post_date'=>$now,
                'post_content' => $post->getContent()
            );

            if ($post->getId()) {
                // The comment has already been saved : update it
                $this->getDb()->update('t_post', $postData, array('post_id' => $post->getId()));
            } else {
            // The comment has never been saved : insert it
                $this->getDb()->insert('t_post', $postData);
                // Get the id of the newly created comment and set it on the entity.
                $id = $this->getDb()->lastInsertId();
                $post->setId($id);
            }
        }
        public function delete($id) {
            // Delete the comment
            $this->getDb()->delete('t_post', array('post_id' => $id));
        }
        /**
        * Return a list of all comments for an article, sorted by date (most recent last).
        *
        * @param integer $articleId The article id.
        *
        * @return array A list of all comments for the article.
        */
        public function findAllByTopic($topicId) {
            // The associated article is retrieved only once
            $topic = $this->topicDAO->find($topicId);
            
            // art_id is not selected by the SQL query
            // The article won't be retrieved during domain objet construction
            $sql = "select *
                    from t_post 
                    where top_id=? ";
            $result = $this->getDb()->fetchAll($sql, array($topicId));

            // Convert query result to an array of domain objects
            $posts = array();
            foreach ($result as $row) {
                $postId = $row['post_id'];
                $post = $this->buildDomainObject($row);
                // The associated article is defined for the constructed comment
                $post->setTopic($topic);
                $posts[$postId] = $post;
            }
            return $posts;
        }

        public function CountPost() {
            $sql = "SELECT
                    T.top_id,
                    T.nbPost,
                    P.post_date as lastDatePost,
                    U.usr_name
                    FROM (
                    -- Dernier post_id et nombre de posts par topic
                    SELECT top_id, MAX(post_id) as max_post, COUNT(*) as nbpost
                    FROM t_post
                    GROUP BY top_id
                    ) T
                    INNER JOIN t_post P
                    ON T.max_post = P.post_id
                    INNER JOIN t_user U
                    ON P.usr_id = U.usr_id";
            $result = $this->getDb()->fetchAll($sql);

            // Convert query result to an array of domain objects
            $messages = array();
            foreach ($result as $row) {
                $topId = $row['top_id'];
                $messages[$topId] = $this->buildDomainObject2($row);
            }
            return $messages;
        }

        public function deleteAllByUser($userId) {
            $this->getDb()->delete('t_post', array('usr_id' => $userId));
        }

        public function changeAllByUser($userId) {
            $postData = array(
                'usr_id' => 404,
            );
            $this->getDb()->update('t_post', $postData, array('usr_id' => $userId));
        }

        public function SortDatePosts() {
            $sql = "SELECT  T.cat_id, 
                    P.post_date, 
                    P.top_id, 
                    T.top_title

                    FROM (
                    -- top_id, post_date et date la plus récente
                    SELECT top_id, MAX(post_date) as post_date
                    FROM t_post
                    GROUP BY top_id
                    ) 	P, 
                    t_topic T

                    WHERE P.top_id = T.top_id
                    UNION
                    SELECT  T.cat_id, 
                    T.top_date, 
                    T.top_id, 
                    T.top_title
                    FROM (
                    SELECT top_id, MAX(top_date) as top_date, top_title, cat_id
                    FROM t_topic
                    GROUP BY top_id
                    ) 	T 
                    ORDER BY post_date desc";
            $result = $this->getDb()->fetchAll($sql);

            // Convert query result to an array of domain objects
            $SortDatePosts = array();
            foreach ($result as $row) {
                $topId = $row['top_id'];
                $SortDatePosts[$topId] = $this->buildDomainObject3($row);
            }
            return $SortDatePosts;
        } 
        /**
        * Creates an Comment object based on a DB row.
        *
        * @param array $row The DB row containing Comment data.
        * @return \MicroCMS\Domain\Comment
        */
        protected function buildDomainObject(array $row) {
            $post = new Post();
            $post->setId($row['post_id']);
            $post->setContent($row['post_content']);
            $post->setDate($row['post_date']);

            if (array_key_exists('topic_id', $row)) {
                // Find and set the associated article
                $topicId = $row['topic_id'];
                $topic = $this->topicDAO->find($topicId);
                $post->setTopic($topic);
            }

            if (array_key_exists('usr_id', $row)) {
                // Find and set the associated author
                $userId = $row['usr_id'];
                $user = $this->userDAO->find($userId);
                $post->setAuthor($user);
            }

            return $post;
        }
        protected function buildDomainObject2(array $row) {
            $post2 = new Post();
            $post2->setId($row['top_id']);
            $post2->setNbPost($row['nbPost']);
            $post2->setLastDatePost($row['lastDatePost']);
            $post2->setAuthor_name($row['usr_name']);

            return $post2;
        }

        protected function buildDomainObject3(array $row) {
            $post3 = new Post();
            //        $post3->setId($row['post_id']);
            $post3->setTopId($row['top_id']);
            $post3->setDate($row['post_date']);
            $post3->setCatId($row['cat_id']);        
            $post3->setTitle($row['top_title']);
            return $post3;
        }
    }