<?php

namespace forum_culinaire\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{

    private $id;

    private $username;

    private $password;

    private $salt;

    private $role;

    private $picture;
    
    protected $file;

    /**
     * @param UploadedFile $file - Uploaded File
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }

        public function getPicture()
    {
        return $this->picture;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        // Nothing to do here
    }
}