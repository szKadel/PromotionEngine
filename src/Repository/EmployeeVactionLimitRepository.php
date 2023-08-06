<?php

namespace App\Repository;

use App\Entity\EmployeeVactionLimit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmployeeVactionLimit>
 *
 * @method EmployeeVactionLimit|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmployeeVactionLimit|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmployeeVactionLimit[]    findAll()
 * @method EmployeeVactionLimit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeVactionLimitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeVactionLimit::class);
    }

//    /**
//     * @return EmployeeVactionLimit[] Returns an array of EmployeeVactionLimit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmployeeVactionLimit
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
