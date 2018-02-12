<?php

namespace forum_culinaire\Domain;

class Post 
{

    private $id;
    private $topId;
    private $catId;

    private $content;
    
    private $date;
    
    private $author;

    private $nbPost;
    
    private $lastDatePost;
    
    private $author_name;
    
    private $title;
    
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }       
    
    public function getTopId() {
        return $this->topId;
    }

    public function setTopId($topId) {
        $this->topId = $topId;
        return $this;
    }   
    
    public function getCatId() {
        return $this->catId;
    }

    public function setCatId($catId) {
        $this->catId = $catId;
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
    
    public function getNbPost() {
        return $this->nbPost;
    }

    public function setLastDatePost($lastDatePost) {
        $this->lastDatePost = $lastDatePost;
        return $this;
    }
    
    public function getAuthor_name() {
        return $this->author_name;
    }
    
    public function setAuthor_name($author_name) {
        $this->author_name = $author_name;
        return $this;
    }
    
    public function getLastDatePost() {
        return $this->lastDatePost;
    }

    public function setNbPost($nbPost) {
        $this->nbPost = $nbPost;
        return $this;
    }


    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
        return $this;
    }
    

    public function getTopic() {
        return $this->topic;
    }

    public function setTopic(Topic $topic) {
        $this->topic = $topic;
        return $this;
    }
}
