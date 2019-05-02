<?php


namespace App\Repository;


use App\Entity\Commentaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentaireRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function findCommentaire($episode)
    {
        $query = $this->createQueryBuilder('commentaire');
        $query->innerJoin('commentaire.episode', 'episode', Join::WITH, 'episode.idEpisodeApi = :episode')
            ->setParameter('episode', $episode);


        return $query->getQuery()->getResult();
    }
}