<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-11
 * Time: 21:09
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class tvShow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $idApi;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="tvShows")
     * @var ArrayCollection
     */
    private $users;

    /**
     * tvShow constructor.
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
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return int
     */
    public function getIdApi(): int
    {
        return $this->idApi;
    }

    /**
     * @param int $idApi
     */
    public function setIdApi(int $idApi): void
    {
        $this->idApi = $idApi;
    }

    /**
     * @param User $user
     * @return ArrayCollection
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
        return $this->users;
    }

    /**
     * @param User $user
     * @return ArrayCollection
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        return $this->users;
    }

}