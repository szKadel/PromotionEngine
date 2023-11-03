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

class UserTest extends KernelTestCase
{

    use HasBrowser;
    use ResetDatabase;


    public function testUserPut()
    {
        $department = DepartmentFactory::createMany(5);
        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['password'=>'pass','employee' => null, 'roles'=>[]]);
        $kadr = UserFactory::createOne(['password'=>'pass','employee' => null, 'roles'=>['ROLE_KADR']]);
        $admin = UserFactory::createOne(['password'=>'pass','employee' => null, 'roles'=>['ROLE_ADMIN']]);


        $this->browser()
            ->actingAs($kadr)
            ->put('/api/users/'.$user->getId(),[
                    'json'=>[
                        'userName' => 'test'
                    ]
                ]
            )->assertStatus(200);

        $this->browser()
            ->actingAs($kadr)
            ->put('/api/users/'.$kadr->getId(),[
                    'json'=>[
                        'userName' => 'test'
                    ]
                ]
            )->assertStatus(400);

        $this->browser()
            ->actingAs($user)
            ->put('/api/users/'.$user->getId(),[
                    'json'=>[
                        'userName' => 'test'
                    ]
                ]
            )->assertStatus(403);

    }
    public function testDeleteUser()
    {
        $department = DepartmentFactory::createMany(5);
        $employee = EmployeeFactory::createOne();
        $employee2 = EmployeeFactory::createOne();
        $employee3 = EmployeeFactory::createOne();
        $employee4 = EmployeeFactory::createOne();

        $vacationType = VacationTypesFactory::createOne([
            'name'=>'Urlop Wypoczynkowy'
        ]);
        $vacationType2 = VacationTypesFactory::createOne();

        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);
        VacationLimitsFactory::createOne(["employee"=>$employee,'vacationType'=>$vacationType2, 'daysLimit'=>500]);

        $user = UserFactory::createOne(['employee'=>$employee2,'password'=>'pass','roles'=>['ROLE_ADMIN']]);
        $user2 = UserFactory::createOne(['employee'=>$employee3,'password'=>'pass']);
        $user3 = UserFactory::createOne(['employee'=>$employee4,'password'=>'pass']);
        $user3 = UserFactory::createOne(['employee'=>$employee,'password'=>'pass']);

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>$user->getEmail(),
                'password'=>'pass'
            ]
            ])->assertStatus(200)->assertAuthenticated();

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>$user->getEmail(),
                'password'=>'pass'
            ]
            ])->assertStatus(200)->assertAuthenticated();

        VacationFactory::createOne(['employee' => $employee, 'type'=>$vacationType]);
        VacationFactory::createMany(5,['employee' => $employee, 'type'=>$vacationType]);

        $this->browser()
            ->actingAs($user)
            ->get("api/users")
            ->assertJsonMatches('"hydra:totalItems"',4);

        $this->browser()
            ->actingAs($user)
            ->delete('/api/user/custom/1',[])
            ->assertStatus(200);

        $this->browser()
            ->actingAs($user)
            ->delete('/api/user/custom/3',[])
            ->assertStatus(200);

        $this->browser()
            ->actingAs($user)
            ->delete('/api/user/custom/3',[])
            ->assertStatus(400);

        $this->browser()
            ->actingAs($user)
            ->get("api/users")
            ->assertJsonMatches('"hydra:totalItems"',2);

        //$this->browser()->actingAs($user)->delete("/api/users/".$user2->getId())->dump();
    }
}