<?php

namespace App\Repository;

use App\Entity\Company\Employee;
use App\Entity\Vacation\VacationLimits;
use App\Entity\Vacation\VacationTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VacationLimits>
 *
 * @method VacationLimits|null find($id, $lockMode = null, $lockVersion = null)
 * @method VacationLimits|null findOneBy(array $criteria, array $orderBy = null)
 * @method VacationLimits[]    findAll()
 * @method VacationLimits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeVacationLimitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacationLimits::class);
    }

    public function findLimitByTypes(Employee $employee,VacationTypes $types)
    {
        return $this->createQueryBuilder('e')
            ->select()
            ->andWhere('e. vacationType = :type')
            ->andWhere('e. Employee = :employee')
            ->setParameter('type', $types)
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return VacationLimits[] Returns an array of VacationLimits objects
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

//    public function findOneBySomeField($value): ?VacationLimits
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
