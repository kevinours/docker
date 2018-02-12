<?php

namespace forum_culinaire\DAO;

use forum_culinaire\Domain\Topic;

class TopicDAO extends DAO 
{
    /**
     * @var \MicroCMS\DAO\ArticleDAO
     */
    private $CategorieDAO;
    
     private $userDAO;
    
    public function setCategorieDAO(CategorieDAO $categorieDAO) {
        $this->categorieDAO = $categorieDAO;
    }

    
    public function setUserDAO(UserDAO $userDAO) {

        $this->userDAO = $userDAO;

    }
    /**
     * Return a list of all comments for an article, sorted by date (most recent last).
     *
     * @param integer $articleId The article id.
     *
     * @return array A list of all comments for the article.
     */
    public function findAllByCategorie($categorieId) {
        // The associated article is retrieved only once
        $categorie = $this->categorieDAO->find($categorieId);

        // art_id is not selected by the SQL query
        // The article won't be retrieved during domain objet construction
        $sql = "select top_title,top_content, t.top_id, top_date, usr_name as author_name, usr_picture as author_picture
                from t_topic t
                natural join t_user 
                where cat_id=".$categorieId."
                
               union
               select top_title,top_content, top_id, post_date, usr_name as author_name, usr_picture as author_picture
                from t_post
                natural join t_user 
                natural join t_topic
                where cat_id=".$categorieId."
                order by top_date desc";
        $result = $this->getDb()->fetchAll($sql, array($categorieId));

        // Convert query result to an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $topId = $row['top_id'];
            $topic = $this->buildDomainObject($row);
            // The associated article is defined for the constructed comment
            $topic->setCategorie($categorie);
            $topics[$topId] = $topic;
        }
        if(isset($topics))
        return $topics;
    }

        public function find($top_id) {
        $sql = "select top_title,top_content, top_id, top_date, usr_name as author_name, usr_picture as author_picture
                from t_topic
                natural join t_user
                where top_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($top_id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $top_id);
    }
    
    
        public function save(Topic $topic) {
            $now=date("Y-m-j H:i");
            $topicData = array(
                'top_title' => $topic->getTitle(),
                'cat_id' => $topic->getCategorie()->getId(),
                'usr_id' => $topic->getAuthor()->getId(),
                'top_content' => $topic->getContent(),
                'top_date' => $now
                );

            $this->getDb()->insert('t_topic', $topicData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $topic->setId($id);
                return $id;
        }
    
    
    public function deleteAllByUser($userId) {

        $this->getDb()->delete('t_topic', array('usr_id' => $userId));

    }

    
    public function changeAllByUser($userId) {
        $postData = array(
            'usr_id' => 404,
        );
        $this->getDb()->update('t_topic', $postData, array('usr_id' => $userId));
    }
    
    
    public function delete($id) {
        // Delete the comment
        $this->getDb()->delete('t_post', array('top_id' => $id));
        $this->getDb()->delete('t_topic', array('top_id' => $id));
    }
        
    public function search($content) {
        $sql = "select cat_id, top_id, top_title, top_date, top_content, usr_name as author_name
                from t_topic
                natural join t_user
                where top_title LIKE '%".$content."%'";
              $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['top_id'];
            $entities[$id] = $this->buildDomainObject2($row);
        }
        return $entities;
    }
    
    
    
    /**
     * Creates an Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \MicroCMS\Domain\Comment
     */
    protected function buildDomainObject(array $row) {
        $topic = new Topic();
        $topic->setId($row['top_id']);
        $topic->setTitle($row['top_title']);
        $topic->setContent($row['top_content']);
        $topic->setAuthor_name($row['author_name']);
        $topic->setAuthor_picture($row['author_picture']);
        $topic->setDate($row['top_date']);
        
        if (array_key_exists('cat_id', $row)) {
            // Find and set the associated article
            $categorieId = $row['cat_id'];
            $categorie = $this->categorieDAO->find($categorieId);
            $topic->setCategorie($categorie);
        }
        
        return $topic;
    }    
        
    
    protected function buildDomainObject2(array $row) {
        $topic2 = new Topic();
        $topic2->setId($row['top_id']);
        $topic2->setTitle($row['top_title']);
        $topic2->setContent($row['top_content']);
        $topic2->setAuthor_name($row['author_name']);
        $topic2->setDate($row['top_date']);
        if (array_key_exists('cat_id', $row)) {
            // Find and set the associated article
            $categorieId = $row['cat_id'];
            $categorie = $this->categorieDAO->find($categorieId);
            $topic2->setCategorie($categorie);
        }
        return $topic2;
    }    
    

}