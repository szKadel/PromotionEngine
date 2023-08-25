<?php

namespace App\Controller;

use App\Controller\Presist\DepartmentPresist;
use App\Controller\Presist\EmployeePresist;
use App\Entity\Company\Department;
use App\Entity\Company\Employee;
use App\Repository\DepartmentRepository;
use App\Repository\EmployeeRepository;
use App\Service\BitrixService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

// TODO Make BitrixLib as an bundle
class BitrixController extends AbstractController
{
    #[Route('/bitrix/migration/users')]
    public function migrateUsers(
        BitrixService $bitrixService,
        EmployeePresist $employeePresist,
        DepartmentRepository $departmentRepository,
        EmployeeRepository $userRepository
    ):JsonResponse
    {
        $result = $bitrixService->call('user.get',[]);

        $final_result = [];

        $countBatches = ceil($result['total'] / 50);

        $start = 0;
        while($countBatches > 0) {
            $result = $bitrixService->call('user.get?start='.$start, []);
            $final_result[] = 'user.get?start='.$start;
            foreach ($result["result"] as $elemnt) {

                if($userRepository->findOneByBitrixId($elemnt["ID"])!== null){
                    $final_result[] = $elemnt["ID"];
                }else {
                    $user = new Employee();
                    $user->setName($elemnt["NAME"]);
                    $user->setSurname($elemnt["LAST_NAME"]);
                    $user->setEmail($elemnt["EMAIL"]);
                    $user->setBitrixId($elemnt["ID"]);
                    $user->setDepartment($departmentRepository->findOneByBitrixIdField($elemnt['UF_DEPARTMENT'][0]));
                    $employeePresist->add($user);

                    $final_result[] = $elemnt["NAME"];
                }
            }
            $countBatches--;
            $start +=50;
        }


        return new JsonResponse($final_result);
    }

    #[Route('/bitrix/migration/departments')]
    public function migrateDepartments(BitrixService $bitrixService,DepartmentRepository $departmentRepository, DepartmentPresist $departmentPresist):JsonResponse
    {
        $result = $bitrixService->call('department.get',[]);

        $final_result = [];

        $countBatches = ceil($result['total'] / 50) + 1;

        $start = 0;
        while($countBatches > 0) {
            $result = $bitrixService->call('department.get?start='.$start, []);

            foreach ($result["result"] as $elemnt) {
                if($departmentRepository->findOneByBitrixIdField($elemnt["ID"])!== null){
                    $final_result[] = $elemnt["ID"];
                }else {
                    $department = new Department();
                    $department->setName($elemnt["NAME"]);
                    $department->setBitrixId($elemnt["ID"]);
                    $departmentPresist->add($department);
                    $final_result[] =  $elemnt["NAME"];

                }
            }
            $countBatches--;
            $start += 50;
        }

        return new JsonResponse($final_result);
    }

    public function migrate()
    {

    }
}