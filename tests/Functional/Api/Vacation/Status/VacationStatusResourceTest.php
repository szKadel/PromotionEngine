<?php

namespace App\Tests\Functional\Api\Vacation\Status;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class VacationStatusResourceTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testGetCollection()
    {
        $department = DepartmentFactory::createOne();
        $employee = EmployeeFactory::createOne(['department'=>$department]);
        $user = UserFactory::createOne(['employee' => $employee, 'roles'=>['ROLE_USER']]);

        $this->browser()
            ->actingAs($user)
            ->get('/api/vacation_statuses',[])
            ->assertStatus(200);
    }
}