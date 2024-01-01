<?php

namespace App\Tests\Functional\Api\Authentication;


use App\Factory\ApiTokenFactory;
use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\Settings\NotificationFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthenticationTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testLogin()
    {
       DepartmentFactory::createMany(5);
       EmployeeFactory::createOne();
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
            ])->assertStatus(200)->assertAuthenticated();
    }

    public function testRegistration()
    {
        DepartmentFactory::createMany(2);
        EmployeeFactory::createOne();
        ApiTokenFactory::createOne();
        NotificationFactory::createOne();
        $user = UserFactory::createOne(['password'=>'pass', 'roles'=>['ROLE_ADMIN'], 'employee' => null]);

        $this->browser()
            ->actingAs($user)
            ->post(
                '/api/users',
                [
                    'json'=>[
                        'email'=>'test5@test.pl',
                        'password'=>'test',
                        'username'=>'test5'
                    ]
                ]
            )->assertStatus(201);

        $this->browser()
            ->actingAs($user)
            ->get(
                '/api/users',
                [
                ]
            )->assertStatus(200);

        $this->browser()
            ->post(
                '/api/users',
                [
                    'json'=>[
                        'email'=>'test@test.pl',
                        'password'=>'test',
                        'username'=>'test'
                    ]
                ]
            )->assertStatus(401);
    }

    public function testCheckUser()
    {
        EmployeeFactory::createOne();
        $user = UserFactory::createOne(['password'=>'pass']);

        $this->browser()
            ->actingAs($user)
            ->get('/api/getCurrentUser/',[
            ]
            )->assertStatus(200);

        $this->browser()
            ->get('/api/getCurrentUser/',[
                ]
            )->assertStatus(401);
    }
}