<?php

namespace App\Repository;

use App\Entity\VacationTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VacationTypes>
 *
 * @method VacationTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method VacationTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method VacationTypes[]    findAll()
 * @method VacationTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacationTypes::class);
    }

//    /**
//     * @return VacationTypes[] Returns an array of VacationTypes objects
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

//    public function findOneBySomeField($value): ?VacationTypes
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
