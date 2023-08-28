<?php

namespace App\Tests\Functional;

use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
use App\Factory\Vacation\VacationFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class VacationRequestResourceTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testVacationGetCollection()
    {

        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['employee' => $employee]);

        VacationFactory::createOne(['employee' => $employee]);
        VacationFactory::createMany(3);

        $this->browser()
            ->actingAs($user)
            ->get('/api/vacations')
            ->assertStatus(200)
        ->assertJsonMatches('"hydra:totalItems"',0);
    }
}