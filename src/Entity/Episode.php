<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-25
 * Time: 14:55
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Episode
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="idEpisodeApi", type="integer", nullable=false)
     */
    private $idEpisodeApi;

    /**
     * @ORM\Column(name="idSerieApi", type="integer", nullable=false)
     */
    private $idSerieApi;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="episodes")
     */
    private $users;

    /**
     * Episode constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
    public function getIdEpisodeApi()
    {
        return $this->idEpisodeApi;
    }

    /**
     * Utilisation que pendant la création
     * @param mixed $idEpisodeApi
     */
    public function setIdEpisodeApi($idEpisodeApi): void
    {
        $this->idEpisodeApi = $idEpisodeApi;
    }

    /**
     * Utilisation que pendant la création
     * @return mixed
     */
    public function getIdSerieApi()
    {
        return $this->idSerieApi;
    }

    /**
     * @param mixed $idSerieApi
     */
    public function setIdSerieApi($idSerieApi): void
    {
        $this->idSerieApi = $idSerieApi;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(User $user)
    {
        $this->users->add($user);
        return $this->users;
    }

    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        return $this->users;
    }


}