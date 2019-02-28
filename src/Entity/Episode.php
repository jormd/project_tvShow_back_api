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
     * @ORM\ManyToMany(targetEntity="User", inversedBy="episodes", cascade={"all"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="episode")
     */
    private $commentaires;

    /**
     * Episode constructor.
     */
    public function __construct($idEpisodeApi, $idSerieApi)
    {
        $this->idEpisodeApi = $idEpisodeApi;
        $this->idSerieApi = $idSerieApi;
        $this->users = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
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
     * @return mixed
     */
    public function getIdSerieApi()
    {
        return $this->idSerieApi;
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
}