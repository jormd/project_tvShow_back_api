<?php


namespace App\Repository;


use App\Entity\Commentaire;
use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GenreRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    public function findGenreTvShow($user)
    {
        $q = $this->createQueryBuilder('genre');

        $q->join('genre.tvShow', 'tvShow')
            ->join('tvShow.users', 'users', Join::WITH, 'users.id = :user')
            ->setParameter('user', $user);

        return $q->getQuery()->getResult();
    }
}