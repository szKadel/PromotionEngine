<?php

namespace App\Repository\Vacation\Settings;

use App\Entity\Vacation\Settings\BankHoliday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BankHoliday>
 *
 * @method BankHoliday|null find($id, $lockMode = null, $lockVersion = null)
 * @method BankHoliday|null findOneBy(array $criteria, array $orderBy = null)
 * @method BankHoliday[]    findAll()
 * @method BankHoliday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankHolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankHoliday::class);
    }

//    /**
//     * @return BankHoliday[] Returns an array of BankHoliday objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BankHoliday
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
