<?php


namespace App\Repository;


use App\Entity\TvShow;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TvShowRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TvShow::class);
    }

    public function findTvShowForUser(User $user)
    {
        $qb = $this->createQueryBuilder('tv');
        $qb->Join('tv.users', 'users', Join::WITH, 'users.id = :user')
            ->setParameter(':user', $user->getId());

        return $qb->getQuery()->getArrayResult();
    }

    public function findTvShowByUserAndId(User $user, $id)
    {
        $qb = $this->createQueryBuilder('tv');
        $qb->Join('tv.users', 'users', Join::WITH, 'users.id = :user')
            ->where($qb->expr()->eq('tv.idApi', $id))
            ->setParameter(':user', $user->getId());

        return $qb->getQuery()->getArrayResult();
    }
}