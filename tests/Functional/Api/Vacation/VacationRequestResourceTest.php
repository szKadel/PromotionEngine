<?php

namespace App\Tests\Functional\Api\Vacation;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
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
        VacationLimitsFactory::createOne(["employee"=>$employeeMod,'vacationType'=>$vacationType, 'daysLimit'=>500]);

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


}