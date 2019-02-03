<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-03
 * Time: 20:27
 */

namespace App\form\data;


use App\Entity\User;

class UserData
{

    private $email;

    private $password;

    private $name;

    private $lastname;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * Méthode permettant de données les valeurs si le formulaire est valide
     *
     * @param User $user
     * @return User
     */
    public function extract(User $user){
        $user->setPassword($this->password);
        $user->setEmail($this->email);
        $user->setLastname($this->lastname);
        $user->setName($this->name);

        return $user;
    }
}