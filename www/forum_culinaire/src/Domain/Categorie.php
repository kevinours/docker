<?php

namespace forum_culinaire\Domain;

class Categorie 
{
    /**
     * Article id.
     *
     * @var integer
     */
    private $id;

    /**
     * Article title.
     *
     * @var string
     */
    private $name;

    private $nbTopic;

    /**
     * Article content.
     *
     * @var string
     */
    private $description;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getNbTopic() {
        return $this->nbTopic;
    }

    public function setNbTopic($nbTopic) {
        $this->nbTopic = $nbTopic;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

}
