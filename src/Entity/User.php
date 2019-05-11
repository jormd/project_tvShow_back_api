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
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\ManyToMany(targetEntity="Episode", mappedBy="users", cascade={"all"})
     */
    private $episodes;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="user")
     */
    private $commentaires;

    /**
     * @ORM\Column(name="co_google", type="boolean", options={"default": false})
     */
    private $coGoogle;

    // ...
    /**
     * One Category has Many Categories.
     * @ORM\OneToMany(targetEntity="User", mappedBy="parent")
     */
    private $friends;

    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     */
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Genre", mappedBy="users", cascade={"all"})
     */
    private $genres;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tvShows = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->friends = new ArrayCollection();
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

    public function getTvShows()
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

    /**
     * @param Episode $episode
     * @return ArrayCollection
     */
    public function addEpisode(Episode $episode)
    {
        $this->episodes->add($episode);
        return $this->episodes;
    }

    /**
     * @param Episode $episode
     * @return ArrayCollection
     */
    public function removeEpisode(Episode $episode)
    {
        $this->episodes->removeElement($episode);
        return $this->episodes;
    }

    /**
     * @return mixed
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * @param Commentaire $commentaire
     * @return ArrayCollection
     */
    public function addCommentaire(Commentaire $commentaire)
    {
        $this->commentaires->add($commentaire);
        return $this->commentaires;
    }

    /**
     * @param Commentaire $commentaire
     * @return ArrayCollection
     */
    public function removeCommentaire(Commentaire $commentaire)
    {
        $this->commentaires->remove($commentaire);
        return $this->commentaires;
    }

    /**
     * @return mixed
     */
    public function getCoGoogle()
    {
        return $this->coGoogle;
    }

    /**
     * @param mixed $coGoogle
     */
    public function setCoGoogle($coGoogle): void
    {
        $this->coGoogle = $coGoogle;
    }

    /**
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    public function addGenre($genre)
    {
        $this->genres->add($genre);
        return $this->genres;
    }

    public function removeGenre($genre)
    {
        $this->genres->removeElement($genre);
        return $this->genres;
    }

    /**
     * @return mixed
     */
    public function getFriends()
    {
        return $this->friends;
    }

    public function addFriends($user)
    {
        $this->friends->add($user);
        return $this->friends;
    }

    public function removeFriends($user)
    {
        $this->friends->removeElement($user);
        return $this->friends;
    }

}
