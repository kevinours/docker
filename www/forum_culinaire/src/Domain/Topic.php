<?php

namespace forum_culinaire\Domain;

class Topic 
{

    private $id;

    private $title;

    private $content;
    
    private $author; 
    
    private $author_picture;
    
    private $author_name ;
    
    private $date;
    
    private $categorie;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
    
    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }
    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
        return $this;
    }
    
    public function getAuthor_name() {
        return $this->author_name;
    }
    
    public function setAuthor_name($author_name) {
        $this->author_name = $author_name;
        return $this;
    }
    
    public function getAuthor_picture() {
        return $this->author_picture;
    }


    public function setAuthor_picture($author_picture) {
        $this->author_picture = $author_picture;
        return $this;
    }
    
    public function getCategorie() {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie) {
        $this->categorie = $categorie;
        return $this;
    }
}
