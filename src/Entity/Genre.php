<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Genre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nom", type="string")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="genres", cascade={"all"})
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="TvShow", mappedBy="genres", cascade={"all"})
     */
    private $tvShow;

    /**
     * Genre constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tvShow = new ArrayCollection();
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
    public function getUsers()
    {
        return $this->users;
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

    /**
     * @return mixed
     */
    public function getTvShow()
    {
        return $this->tvShow;
    }

    public function addTvShow(TvShow $tvShow)
    {
        $this->tvShow->add($tvShow);
        return $this->tvShow;
    }

    public function removeTvShow(TvShow $tvShow)
    {
        $this->tvShow->removeElement($tvShow);
        return $this->tvShow;
    }
}