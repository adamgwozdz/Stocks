<?php

namespace App\Repository;

use App\Entity\UserMoney;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMoney|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMoney|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMoney[]    findAll()
 * @method UserMoney[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMoneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMoney::class);
    }

    // /**
    //  * @return UserMoney[] Returns an array of UserMoney objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserMoney
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
