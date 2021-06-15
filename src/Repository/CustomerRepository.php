<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }


    public function findAllByUser($user)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->andWhere('u.user = :val')
            ->setParameter('val', $user)
            ->select('u');
        return $qb->getQuery()->getArrayResult();
    }

    public function findOneByUser($user, $useruniqueid)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->andWhere('u.user = :val', 'u.id = :userid')
            ->setParameter('val', $user)
            ->setParameter('userid', $useruniqueid)
            ->select('u');
        return $qb->getQuery()->getArrayResult();
    }

    public function findForRemoveByUser($user, $useruniqueid)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->andWhere('u.user = :val', 'u.id = :userid')
            ->setParameter('val', $user)
            ->setParameter('userid', $useruniqueid)
            ->select('u');
        return $qb->getQuery()->getResult();
    }

    public function findByMaxResult($user, $maxresult)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :val')
            ->setParameter('val', $user)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->setFirstResult($maxresult)
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
