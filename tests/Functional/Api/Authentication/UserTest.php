<?php

namespace App\Tests\Functional\Api\Authentication;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
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
}