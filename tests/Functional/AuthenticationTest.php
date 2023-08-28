<?php

namespace App\Tests\Functional;

use App\Factory\ApiTokenFactory;
use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
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
            ])->assertStatus(200)->assertAuthenticated();
    }

    public function testRegistration()
    {
        $department = DepartmentFactory::createMany(5);
        $employee = EmployeeFactory::createOne();
        $token = ApiTokenFactory::createOne();
        $this->browser()
            ->post(
                '/api/users',
                [
                    'json'=>[
                        'email'=>'test@test.pl',
                        'password'=>'test',
                        'username'=>'test'
                    ],
                    'headers'=>[
                        'Authorization'=>'Bearer '. $token->getToken()
                    ]
                ]
            )->assertStatus(201);

        //LogIn

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

        $this->browser()
            ->post('/login',['json'=>[
                'email'=>'test@test.pl',
                'password'=>'test'
            ]
            ])->assertStatus(200);
    }
}