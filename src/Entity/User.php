<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-01-30
 * Time: 15:26
 */

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="user")
 * Class User
 * @package App\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tokenApi;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTokenApi()
    {
        return $this->tokenApi;
    }

    /**
     * @param mixed $tokenApi
     */
    public function setTokenApi($tokenApi): void
    {
        $this->tokenApi = $tokenApi;
    }


}