<?php


namespace App\Repository;


use App\Entity\Episode;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EpisodeRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    public function findEpisodeSee(User $user, $idSerie, $idEpisode)
    {
        $query = $this->createQueryBuilder('episode');
        $query->innerJoin('episode.users', 'user', Join::WITH, 'user.id = :user')
            ->where($query->expr()->eq('episode.idSerieApi', $idSerie))
            ->andWhere($query->expr()->eq('episode.idEpisodeApi', $idEpisode))
            ->setParameter('user', $user->getId());

        return $query->getQuery()->getArrayResult();
    }
}