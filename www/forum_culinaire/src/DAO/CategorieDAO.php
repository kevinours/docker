<?php

namespace forum_culinaire\DAO;

use forum_culinaire\Domain\Categorie;

class CategorieDAO extends DAO
{
    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select *, count(top_id) as nbTopic 
                from t_categorie
                natural join t_topic 
                group by cat_id";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $categories = array();
        foreach ($result as $row) {
            $categorieId = $row['cat_id'];
            $categories[$categorieId] = $this->buildDomainObject($row);
        }
        return $categories;
    }

    /**
     * Returns an article matching the supplied id.
     *
     * @param integer $id The article id.
     *
     * @return \MicroCMS\Domain\Article|throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "select *, count(top_id) as nbTopic 
                from t_categorie
                natural join t_topic 
                where cat_id=?
                group by cat_id";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $id);
    }


    
    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \MicroCMS\Domain\Article
     */

    protected function buildDomainObject(array $row) {
        $categorie = new Categorie();
        $categorie->setId($row['cat_id']);
        $categorie->setName($row['cat_name']);
        $categorie->setDescription($row['cat_description']);
        $categorie->setNbTopic($row['nbTopic']);        
        return $categorie;
    }
}
