<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationStatus;
use App\Entity\Vacation\VacationTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @extends ServiceEntityRepository<\App\Entity\Vacation\Vacation>
 *
 * @method Vacation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacation[]    findAll()
 * @method Vacation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationRepository extends ServiceEntityRepository
{
    private Security $security;

    public function __construct(ManagerRegistry $registry,private VacationStatusRepository $vacationStatusRepository,Security $security)
    {
        parent::__construct($registry, Vacation::class);
        $this->security = $security;
    }


    public function findExistingVacationForUserInDateRange(Employee $employee, \DateTimeInterface $startDate, \DateTimeInterface $endDate):void
    {
        $statusAccepted = $this->vacationStatusRepository->findByName("Potwierdzony");
        $statusWaiting = $this->vacationStatusRepository->findByName("Oczekujący");

        $result = $this->createQueryBuilder('v')
            ->andWhere('v.employee = :employee')
            ->andWhere('(:dateFrom BETWEEN v.dateFrom AND v.dateTo OR :dateTo BETWEEN v.dateFrom AND v.dateTo OR v.dateFrom BETWEEN :dateFrom AND :dateFrom OR v.dateFrom BETWEEN :dateFrom AND :dateTo OR :dateFrom = v.dateTo OR v.dateTo = :dateFrom OR v.dateFrom = :dateTo)')
            ->setParameter('employee', $employee)
            ->setParameter('dateFrom', $startDate->format('Y-m-d'))
            ->setParameter('dateTo', $endDate->format('Y-m-d'))
            ->getQuery()
            ->getResult();

         if(!empty($result))
         {
             throw new BadRequestException("Wniosek dla ".$employee->getName()." ".$employee->getSurname()." w tym terminie został już złożony");
         }

    }

    public function findVacationsToExtract(Company $company, \DateTime $dateFrom, \DateTime $dateTo)
    {
        $statusAccepted = $this->vacationStatusRepository->findByName("Potwierdzony");

        return $this->createQueryBuilder('v')
            ->leftJoin('v.employee', "e")
            ->andWhere('e.company = :company')
            ->andWhere('(v.dateTo BETWEEN :dateFrom AND :dateTo OR
             v.dateFrom BETWEEN :dateFrom AND :dateTo OR 
             :dateFrom BETWEEN v.dateFrom AND v.dateTo OR 
             :dateTo BETWEEN v.dateFrom AND v.dateTo OR 
             v.dateFrom BETWEEN :dateFrom AND :dateFrom OR 
             v.dateFrom BETWEEN :dateFrom AND :dateTo OR
             :dateFrom = v.dateTo OR
              v.dateTo = :dateFrom OR
              v.dateFrom = :dateTo)')
            ->andWhere('v.status = :status')
            ->setParameter('status', $statusAccepted)
            ->setParameter('company', $company)
            ->setParameter('dateFrom', $dateFrom->format('Y-m-d'))
            ->setParameter('dateTo', $dateTo->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findVacationUsedByUser(Employee $employee, VacationTypes $vacationTypes):int
    {
        $statusAccepted = $this->vacationStatusRepository->findByName("Potwierdzony");

        $result = $this->createQueryBuilder('v')
            ->andWhere('v.employee = :employee')
            ->andWhere('v.status = :status')
            ->andWhere('v.type = :types')
            ->setParameter('employee', $employee)
            ->setParameter('status', $statusAccepted)
            ->setParameter('types', $vacationTypes)
            ->getQuery()
            ->getResult();
        $days = 0;

        if(!empty($result)) {
            foreach ($result as $element) {
                $days += $element->getSpendVacationDays();
            }
        }

        return $days;
    }

    public function findVacationUsedByUserArray(Employee $employee, VacationTypes $vacationTypes):array | null
    {
        $statusAccepted = $this->vacationStatusRepository->findByName("Potwierdzony");

        return  $this->createQueryBuilder('v')
            ->andWhere('v.employee = :employee')
            ->andWhere('v.status = :status')
            ->andWhere('v.type = :types')
            ->setParameter('employee', $employee)
            ->setParameter('status', $statusAccepted)
            ->setParameter('types', $vacationTypes)
            ->getQuery()
            ->getResult();
    }

    public function findEmployeeOnVacation(string $dateFrom, string $dateTo) :mixed
    {
        $statusAccepted = $this->vacationStatusRepository->findByName("Potwierdzony");

        return $this->createQueryBuilder('v')
            ->leftJoin('v.employee', "e")
            ->andWhere('e.department = :department')
            ->andWhere('(v.dateTo BETWEEN :dateFrom AND :dateTo OR
             v.dateFrom BETWEEN :dateFrom AND :dateTo OR 
             :dateFrom BETWEEN v.dateFrom AND v.dateTo OR 
             :dateTo BETWEEN v.dateFrom AND v.dateTo OR 
             v.dateFrom BETWEEN :dateFrom AND :dateFrom OR 
             v.dateFrom BETWEEN :dateFrom AND :dateTo OR
             :dateFrom = v.dateTo OR
              v.dateTo = :dateFrom OR
              v.dateFrom = :dateTo)')
            ->andWhere('v.status = :status')
            ->setParameter('status', $statusAccepted)
            ->setParameter('department', $this->security->getUser()->getEmployee()->getDepartment())
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getResult();
    }

    public function findEmployeeOnVacationForAdmin(string $dateFrom, string $dateTo) :mixed
    {
        $statusAccepted = $this->vacationStatusRepository->findByName("Potwierdzony");

        return $this->createQueryBuilder('v')
            ->andWhere('(v.dateTo BETWEEN :dateFrom AND :dateTo OR
             v.dateFrom BETWEEN :dateFrom AND :dateTo OR 
             :dateFrom BETWEEN v.dateFrom AND v.dateTo OR 
             :dateTo BETWEEN v.dateFrom AND v.dateTo OR 
             v.dateFrom BETWEEN :dateFrom AND :dateFrom OR 
             v.dateFrom BETWEEN :dateFrom AND :dateTo OR
             :dateFrom = v.dateTo OR
              v.dateTo = :dateFrom OR
              v.dateFrom = :dateTo)')
            ->andWhere('v.status = :status')
            ->setParameter('status', $statusAccepted)
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Vacation[] Returns an array of Vacation objects
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

//    public function findOneBySomeField($value): ?Vacation
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
