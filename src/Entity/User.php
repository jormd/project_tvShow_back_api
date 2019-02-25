<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tokenApi;

    /**
     * @ORM\ManyToMany(targetEntity="TvShow", mappedBy="users", cascade={"all"})
     */
    private $tvShows;

    /**
     * @ORM\ManyToMany(targetEntity="Episode", mappedBy="users")
     */
    private $episodes;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tvShows = new ArrayCollection();
        $this->episodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /**
     * @return ArrayCollection
     */
    public function getTvShows(): ArrayCollection
    {
        return $this->tvShows;
    }

    /**
     * @param TvShow $tvShow
     * @return ArrayCollection
     */
    public function addTvShow(TvShow $tvShow)
    {
        $this->tvShows->add($tvShow);
        return $this->tvShows;
    }

    /**
     * @param TvShow $tvShow
     * @return ArrayCollection
     */
    public function removeTvShow(TvShow $tvShow)
    {
        $this->tvShows->removeElement($tvShow);
        return $this->tvShows;
    }

    /**
     * @return mixed
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode)
    {
        $this->episodes->add($episode);
        return $this->episodes;
    }

    public function removeEpisode(Episode $episode)
    {
        $this->episodes->removeElement($episode);
        return $this->episodes;
    }
}
