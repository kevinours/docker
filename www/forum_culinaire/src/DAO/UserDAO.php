<?php

namespace forum_culinaire\DAO;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use forum_culinaire\Domain\User;

class UserDAO extends DAO implements UserProviderInterface
{
    /**
     * Returns a user matching the supplied id.
     *
     * @param integer $id The user id.
     *
     * @return \MicroCMS\Domain\User|throws an exception if no matching user is found
     */
    public function find($id) {
        $sql = "select * from t_user where usr_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No user matching id " . $id);
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $sql = "select * from t_user where usr_name=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));

        if ($row){
             $_SESSION['id']=$row['usr_id'];
            return $this->buildDomainObject($row);}
        else
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'forum_culinaire\Domain\User' === $class;
    }

    /**
     * Creates a User object based on a DB row.
     *
     * @param array $row The DB row containing User data.
     * @return \MicroCMS\Domain\User
     */
    
    
    public function findAll() {
        $sql = "select * from t_user order by usr_role, usr_name";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['usr_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }
            
    
    public function findAllOnline() {
        $sql = "select * from t_user where usr_online = '1'";
        $result = $this->getDb()->fetchAll($sql);
        return $result;
    }
    
        public function save(User $user) {
            if(null!==($user->getRole()))
                $userRole = $user->getRole();
            else
                $userRole = 'ROLE_USER';
            
        $userData = array(
            'usr_name' => $user->getUsername(),
            'usr_salt' => $user->getSalt(),
            'usr_password' => $user->getPassword(),
            'usr_role' => $userRole
            );

        if ($user->getId()) {
            // The user has already been saved : update it
            $this->getDb()->update('t_user', $userData, array('usr_id' => $user->getId()));
        } else {
            // The user has never been saved : insert it
            $this->getDb()->insert('t_user', $userData);
            // Get the id of the newly created user and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }
    
        public function savePicture($picture, $id) {
        $userData = array(
            'usr_picture' => $picture
            );
            $this->getDb()->update('t_user', $userData, array('usr_id' => $id));
    }

    
            public function pictureToDelete($id) {
                $sql = "select usr_picture
                        from t_user 
                        where usr_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));
                return $row['usr_picture'];
    }
    
    public function isOnline($id) {
                $userData = array(
            'usr_online' => 1
            );
        $this->getDb()->update('t_user', $userData, array('usr_id' => $id));
    }    
    
    public function isOffline($id) {
                $userData = array(
            'usr_online' => 0
            );
        $this->getDb()->update('t_user', $userData, array('usr_id' => $id));
    }
    /**
     * Removes a user from the database.
     *
     * @param @param integer $id The user id.
     */
    public function delete($id) {
        // Delete the user

        $this->getDb()->delete('t_user', array('usr_id' => $id));
    }
    protected function buildDomainObject(array $row) {
        $user = new User();
        $user->setId($row['usr_id']);
        $user->setUsername($row['usr_name']);
        $user->setPassword($row['usr_password']);
        $user->setPicture($row['usr_picture']);
        $user->setSalt($row['usr_salt']);
        $user->setRole($row['usr_role']);
        return $user;
    }
}