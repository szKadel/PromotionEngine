<?php

namespace App\Tests\Functional;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class EmployeeResourceTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

//    public function testGetCollectionOfEmployees()
//    {
//        DepartmentFactory::createMany(10);
//        EmployeeFactory::createMany(2);
//
//        $this->browser()
//            ->get('/api/employees')
//            ->assertjson()
//            ->assertStatus(401);
//    }

    public function testPutToUpdateEmployee()
    {
        $department = DepartmentFactory::createMany(5);

        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['employee' => $employee]);

        EmployeeFactory::createMany(5);
        $employee2 = EmployeeFactory::createOne();
        $user2 = UserFactory::createOne(['employee' => $employee2]);

        $this->browser()
            ->actingAs($user)
            ->get('/api/employees')
            ->assertStatus(200)
            ->assertJsonMatches('"hydra:totalItems"',1);
    }
}