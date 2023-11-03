<?php

namespace App\Tests\Functional\Api\Authentication;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
use App\Factory\Vacation\VacationFactory;
use App\Factory\Vacation\VacationLimitsFactory;
use App\Factory\VacationTypesFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class ApiTokenTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testLogin()
    {
        $department = DepartmentFactory::createMany(5);
        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['password'=>'pass']);

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>$user->getEmail(),
                'password'=>'pas'
            ]
            ])->assertStatus(401);

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>$user->getEmail(),
                'password'=>'pass'
            ]
            ])
            ->assertStatus(200);

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>$user->getEmail(),
                'password'=>'pass'
            ]
            ])
            ->assertStatus(200);
    }

    public function testDeleteUser()
    {
        $department = DepartmentFactory::createMany(5);
        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['password'=>'pass']);

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>$user->getEmail(),
                'password'=>'pass'
            ]
            ])
            ->assertStatus(200);

        //$this->browser()->actingAs($user)->delete("/api/users/".$user2->getId())->dump();
    }

    public function testDeleteEmployee()
    {
        $department = DepartmentFactory::createMany(5);
        $employee = EmployeeFactory::createOne();
        $employee2 = EmployeeFactory::createOne();
        $employee3 = EmployeeFactory::createOne();

        $vacationType = VacationTypesFactory::createOne();
        $vacationType2 = VacationTypesFactory::createOne();

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employee2,'vacationType'=>$vacationType2, 'daysLimit'=>500]);

        $user = UserFactory::createOne(['employee'=>$employee2,'password'=>'pass','roles'=>['ROLE_ADMIN']]);
        $user2 = UserFactory::createOne(['employee'=>$employee,'password'=>'pass','roles'=>['ROLE_ADMIN']]);


        VacationFactory::createOne(['employee' => $employee3, 'type'=>$vacationType,'replacement'=>$employee]);
        VacationFactory::createOne(['employee' => $employee, 'type'=>$vacationType,'replacement'=>$employee3]);
        VacationFactory::createMany(5,['employee' => $employee, 'type'=>$vacationType, 'replacement'=>$employee2]);

        $this->browser()
            ->actingAs($user2)
            ->get("api/employees")
            ->assertJsonMatches('"hydra:totalItems"',10);

        $this->browser()
            ->actingAs($user2)
            ->delete('/api/employee/custom/1',[])
            ->assertStatus(200);

        $this->browser()
            ->actingAs($user)
            ->get("api/employees")
            ->assertJsonMatches('"hydra:totalItems"',9);

        //$this->browser()->actingAs($user)->delete("/api/users/".$user2->getId())->dump();
    }

}