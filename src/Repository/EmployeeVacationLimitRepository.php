<?php

namespace App\Repository;

use App\Entity\EmployeeVacationLimit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmployeeVacationLimit>
 *
 * @method EmployeeVacationLimit|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmployeeVacationLimit|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmployeeVacationLimit[]    findAll()
 * @method EmployeeVacationLimit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeVacationLimitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeVacationLimit::class);
    }

//    /**
//     * @return EmployeeVacationLimit[] Returns an array of EmployeeVacationLimit objects
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

//    public function findOneBySomeField($value): ?EmployeeVacationLimit
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
