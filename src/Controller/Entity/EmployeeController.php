<?php

namespace App\Controller\Entity;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Company\Department;
use App\Entity\Company\Employee;
use App\Entity\User;
use App\Entity\Vacation\Vacation;
use App\Repository\ApiTokenRepository;
use App\Repository\EmployeeExtendedAccessesRepository;
use App\Repository\EmployeeRepository;
use App\Repository\EmployeeVacationLimitRepository;
use App\Repository\UserRepository;
use App\Repository\VacationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EmployeeController extends AbstractController
{
    public function __construct(
        private EmployeeVacationLimitRepository $employeeVacationLimitRepository,
        private EmployeeRepository $employeeRepository,
        private UserRepository $userRepository,
        private ApiTokenRepository $apiTokenRepository,
        private VacationRepository $vacationRepository,
        private EntityManagerInterface $entityManager,
        private IriConverterInterface $iriConverter,
        private Security $security
    )
    {

    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/employee/custom/{id}', methods: ['DELETE'])]
    public function deleteEmployee($id)
    {
        $employee = $this->employeeRepository->find($id) ;

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
            $employee->setUser(null);
            $this->entityManager->flush();
        }

        $this->delete($employee);

        return new Response("Employee deleted id ".$id,200);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/user/custom/{id}', methods: ['DELETE'])]
    public function deleteUser(int $id, ApiTokenRepository $apiTokenRepository)
    {

        $user = $this->userRepository->find($id);

        if(!$user instanceof User)
        {
            throw new BadRequestException("Nie znaleziono elementu.",400);
        }
        if($user->getEmployee()){
            throw new BadRequestException("Nie można usunać użytkownika z przypisanym pracownikiem.",400);
        }

        $apiTokens = $apiTokenRepository->findBy(["ownedBy"=>$id]);

        if(count($apiTokens) > 1) {
            foreach ($apiTokens as $apiToken) {
                $this->delete($apiToken);
            }
        }elseif (count($apiTokens) == 1){
            $this->delete($apiTokens[0]);
        }

        if(!empty($user->getEmployee())){
            $user->setEmployee(null);
            $this->entityManager->flush();
        }

        $this->entityManager->flush();

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


    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/employee/department/', methods: ['POST'])]
    public function setExternalDepartmentsRight(Request $request, EmployeeExtendedAccessesRepository $employeeExtendedAccessesRepository): Response
    {
        $postData = json_decode($request->getContent());

        $employee = $this->iriConverter->getResourceFromIri($postData ?->iri ?? throw new BadRequestException("Bad Exception"))?? throw new BadRequestException("Bad Exception");

        $departments = $postData ?->departments ?? throw new BadRequestException("Bad Exception");

        foreach ($departments as $department)
        {
            $department = $this->iriConverter->getResourceFromIri($department) ?? throw new BadRequestException("Bad Exception");
            if($department->getId() == $this->security->getUser()?->getEmployee()->getDepartment()->getId()){
                continue;
            }

            if($department instanceof Department)
            {
                $employeeExtendedAccessesRepository->addNew($employee, $department);
            }
        }

        return new Response(json_encode($postData),200);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/employee/department/', methods: ['PUT'])]
    public function updateExternalDepartmentsRight(Request $request, EmployeeExtendedAccessesRepository $employeeExtendedAccessesRepository): Response
    {
        $postData = json_decode($request->getContent());

        $records = $employeeExtendedAccessesRepository->findBy(['employee' => $this->iriConverter->getResourceFromIri($postData ?->iri ?? throw new BadRequestException("Bad Exception"))]);

        foreach ($records as $record) {
            $employeeExtendedAccessesRepository->removeExtendedAccessById($record->getId());
        }

        if(!empty($postData ?->departments)) {
            $this->setExternalDepartmentsRight($request, $employeeExtendedAccessesRepository);
        }

        return new Response(json_encode($postData),200);
    }


}