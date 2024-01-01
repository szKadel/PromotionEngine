<?php

namespace App\Tests\Functional\Api\Vacation;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\Settings\NotificationFactory;
use App\Factory\UserFactory;
use App\Factory\Vacation\VacationFactory;
use App\Factory\Vacation\VacationLimitsFactory;
use App\Factory\Vacation\VacationStatusFactory;
use App\Factory\VacationTypesFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class VacationRequestResourceTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testVacationGetCollection()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);

        $department = DepartmentFactory::createOne();
        $department2 = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department]);
        $employee3 = EmployeeFactory::createOne(['department'=>$department2]);
        $employeeMod = EmployeeFactory::createOne(['department'=>$department]);

        $mod = UserFactory::createOne(['employee' => $employeeMod, 'roles'=>['ROLE_MOD']]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);

        $vacationType = VacationTypesFactory::createOne();

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>20]);

        VacationFactory::createOne(['employee' => $employee, 'type'=>$vacationType]);
        VacationFactory::createMany(5,['employee' => $employee3, 'type'=>$vacationType]);
        VacationFactory::createMany(5,['employee' => $employeeMod, 'type'=>$vacationType]);
        VacationFactory::createMany(5,['employee' => $employee, 'type'=>$vacationType]);

        $this->browser()
            ->actingAs($user)
            ->get('/api/vacations')
            ->assertJsonMatches('"hydra:totalItems"',6)
            ->assertStatus(200);

        $this->browser()
            ->actingAs($mod)
            ->get('/api/vacations')
            ->assertJsonMatches('"hydra:totalItems"',11)
            ->assertStatus(200);

        $this->browser()
            ->get('/api/vacations')
            ->assertStatus(401);
    }

    public function testVacationAdd()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);

        $department = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department]);
        $employeeMod = EmployeeFactory::createOne(['department'=>$department]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);

         VacationTypesFactory::createOne();
        $vacationType = VacationTypesFactory::createOne();

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>20]);

        NotificationFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-15',
                    'dateTo'=>'2023-09-21'
                ]
            ])
            ->assertStatus(201);

        NotificationFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-21',
                    'dateTo'=>'2023-09-22'
                ]
            ])
            ->assertStatus(400);
        NotificationFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-21',
                    'dateTo'=>'2023-09-22'
                ]
            ])
            ->assertStatus(400);
    }


    public function testVacationStatusUpdate()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);
        VacationStatusFactory::createOne(['name'=>'Zaakceptowany']);

        $department = DepartmentFactory::createOne();
        $department2 = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department]);
        EmployeeFactory::createOne(['department'=>$department2]);
        $employeeMod = EmployeeFactory::createOne(['department'=>$department]);

        UserFactory::createOne(['employee' => $employeeMod, 'roles'=>['ROLE_MOD']]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);

        VacationTypesFactory::createOne();
        $vacationType = VacationTypesFactory::createOne(['name'=>'Inny']);
        VacationTypesFactory::createOne(['name'=>'Urlop']);

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>20]);
        NotificationFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-15',
                    'dateTo'=>'2023-09-21'
                ]
            ])
            ->assertStatus(201);

    }

    public function testVacationReplacement()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);
        VacationStatusFactory::createOne(['name'=>'Zaakceptowany']);

        $department = DepartmentFactory::createOne();
        $department2 = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department]);
        $employee2 = EmployeeFactory::createOne(['department'=>$department]);
        EmployeeFactory::createOne(['department'=>$department2]);
        $employeeMod = EmployeeFactory::createOne(['department'=>$department]);

        UserFactory::createOne(['employee' => $employeeMod, 'roles'=>['ROLE_MOD']]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);
        $user2 = UserFactory::createOne(['employee' => $employee2, 'roles'=>['ROLE_USER']]);

        VacationTypesFactory::createOne();
        $vacationType = VacationTypesFactory::createOne(['name'=>'Inny']);
        VacationTypesFactory::createOne(['name'=>'Urlop']);

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>20]);
        VacationLimitsFactory::createOne(["employee"=>$employee2,'vacationType'=>$vacationType, 'daysLimit'=>20]);
        NotificationFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-15',
                    'dateTo'=>'2023-09-21'
                ]
            ])
            ->assertStatus(201);

        $this->browser()
            ->actingAs($user2)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee2->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2023-09-14',
                    'dateTo'=>'2023-09-22',
                    'replacement' => 'api/employees/'.$employee->getId(),
                ]
            ])
            ->assertStatus(400);

    }

    public function  testVacationModExtensionsRight()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);
        VacationStatusFactory::createOne(['name'=>'Zaakceptowany']);

        $department = DepartmentFactory::createOne();
        $department2 = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department2]);
        $employee2 = EmployeeFactory::createOne(['department'=>$department]);
        EmployeeFactory::createOne(['department'=>$department2]);

        $employeeMod = EmployeeFactory::createOne(['department'=>$department, 'employeeExtendedAccesses'=>[]]);

        UserFactory::createOne(['employee' => $employeeMod, 'roles'=>['ROLE_MOD']]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_ADMIN']]);
        UserFactory::createOne(['employee' => $employee2, 'roles'=>['ROLE_USER']]);

        VacationTypesFactory::createOne();
        $vacationType = VacationTypesFactory::createOne(['name'=>'Inny']);
        VacationTypesFactory::createOne(['name'=>'Urlop']);

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>20]);
        VacationLimitsFactory::createOne(["employee"=>$employee2,'vacationType'=>$vacationType, 'daysLimit'=>20]);

        VacationFactory::createMany(5,["employee"=>$employee,'type'=>$vacationType]);
        VacationFactory::createMany(5,["employee"=>$employeeMod,'type'=>$vacationType]);
        VacationFactory::createMany(5,["employee"=>$employee2,'type'=>$vacationType]);

        $this->browser()
            ->actingAs($user)
            ->post('api/employee/department/',[
                'json'=>[
                    'iri'=>'api/employees/'.$employeeMod->getId(),
                    'departments'=> ['api/departments/'.$department->getId()],
                ]
            ])
            ->assertStatus(200);

    }

    public function testVacationsYearCounter()
    {
        VacationStatusFactory::createOne(['name'=>'Oczekujący']);
        VacationStatusFactory::createOne(['name'=>'Zaplanowany']);
        VacationStatusFactory::createOne(['name'=>'Zaakceptowany']);

        $department = DepartmentFactory::createOne();
        $department2 = DepartmentFactory::createOne();

        $employee = EmployeeFactory::createOne(['department'=>$department]);
        $employee2 = EmployeeFactory::createOne(['department'=>$department]);
        EmployeeFactory::createOne(['department'=>$department2]);
        $employeeMod = EmployeeFactory::createOne(['department'=>$department]);

        UserFactory::createOne(['employee' => $employeeMod, 'roles'=>['ROLE_MOD']]);

        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);
        $user2 = UserFactory::createOne(['employee' => $employee2, 'roles'=>['ROLE_USER']]);

        VacationTypesFactory::createOne();
        $vacationType = VacationTypesFactory::createOne(['name'=>'Urlop Wypoczynkowy']);
        VacationTypesFactory::createOne(['name'=>'Urlop']);

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>26]);
        NotificationFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee'=>'api/employees/'.$employee->getId(),
                    'type'=> 'api/vacation_types/'.$vacationType->getId(),
                    'dateFrom'=> '2024-09-15',
                    'dateTo'=>'2024-09-30'
                ]
            ])
            ->assertStatus(201);


        $response = $this->browser()
            ->actingAs($user)
            ->get('/api/getCurrentUser/',[]);

        $jsonResponse = $response->json()->decoded();

        $this->assertEquals(11, $jsonResponse['employee']['spendVacationsDays']);

    }

}