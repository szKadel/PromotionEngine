<?php

namespace App\Repository;

use App\Entity\VacationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VacationStatus>
 *
 * @method VacationStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method VacationStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method VacationStatus[]    findAll()
 * @method VacationStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacationStatus::class);
    }

//    /**
//     * @return VacationStatus[] Returns an array of VacationStatus objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneBySomeField($value): ?VacationStatus
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
