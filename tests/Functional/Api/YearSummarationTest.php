<?php

namespace App\Tests\Functional\Api;

use App\Factory\Company\DepartmentFactory;
use App\Factory\Company\EmployeeFactory;
use App\Factory\VacationTypesFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class YearSummarationTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    public function testCountVacationSumaration()
    {
        $vacationType = VacationTypesFactory::createOne(["name"=>"Urlop Wypoczynkowy"]);

        $department = DepartmentFactory::createOne();
        $employee = EmployeeFactory::createOne(['department'=>$department]);

        $this->browser()->get('/Vacations/YearSummation/2023')->assertStatus(200);
    }
}