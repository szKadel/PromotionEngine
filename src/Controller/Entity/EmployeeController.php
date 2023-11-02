<?php

namespace App\Controller\Entity;

use App\Entity\Company\Employee;
use App\Entity\User;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationLimits;
use App\Repository\EmployeeRepository;
use App\Repository\EmployeeVacationLimitRepository;
use App\Repository\UserRepository;
use App\Repository\VacationRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EmployeeController extends AbstractController
{
    public function __construct(
        private EmployeeVacationLimitRepository $employeeVacationLimitRepository,
        private EmployeeRepository $employeeRepository,
        private UserRepository $userRepository,
        private VacationRepository $vacationRepository,
        private EntityManagerInterface $entityManager
    )
    {

    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/employee/custom/{id}', methods: ['DELETE'])]
    public function deleteEmployee($id)
    {
        $employee = $this->employeeRepository->find($id);

        if(!$employee instanceof Employee)
        {
            throw new BadRequestException("Nie znaleziono elementu.",404);
        }

        $limits =  $this->employeeVacationLimitRepository->findBy(['Employee' => $employee]);

        foreach ($limits as $limit){
            $this->delete($limit);
        }

        $vacations =  $employee->getVacations();

        foreach ($vacations as $vacation){
            $this->delete($vacation);
        }

        $replacements =  $this->vacationRepository->findBy(['replacement' => $employee]);


        $this->entityManager->getRepository(Vacation::class);

        foreach ($replacements as $replacement){
            $replacement->setReplacement(null);
            $this->entityManager->flush();
        }

        if(!empty($employee->getUser())) {
            $employee->getUser()->setEmployee(null);
            $this->entityManager->flush();
        }

        $this->delete($employee);

        return new Response("Employee deleted id ".$id,200);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/user/custom/{id}', methods: ['DELETE'])]
    public function deleteUser($id)
    {
        throw new BadRequestException("Ta funkcjonalność zostanie dodana wkrótce");

        $user = $this->userRepository->find($id);

        if(!$user instanceof User)
        {
            throw new BadRequestException("Nie znaleziono elementu.",404);
        }

        if(!empty($user->getEmployee())){
            $this->deleteEmployee($user->getEmployee()->getId());
        }

        $apiTokens = $user->getApiTokens();

        foreach ($apiTokens as $apiToken)
        {
            $this->delete($apiToken);
        }

        $this->delete($user) ;

        return new Response("User deleted id ".$id,200);
    }

    public function delete(object $object)
    {
        $employee = $this->entityManager->getRepository($object::class)->find($object->getId());

        if (!$employee) {
            throw new BadRequestException('Pracownik o podanym ID nie został znaleziony.');
        }

        $this->entityManager->remove($employee);
        $this->entityManager->flush();

    }


}