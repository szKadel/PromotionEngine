<?php

namespace App\Tests\Functional\Api;

use App\Entity\User;
use App\Entity\Vacation\VacationLimits;
use App\Factory\Company\EmployeeFactory;
use App\Factory\UserFactory;
use App\Factory\Vacation\VacationFactory;
use App\Factory\Vacation\VacationLimitsFactory;
use App\Factory\Vacation\VacationStatusFactory;
use App\Factory\VacationTypesFactory;
use App\Repository\VacationTypesRepository;
use DateTime;
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

        $vacationType = VacationTypesFactory::createOne();
        VacationLimitsFactory::createMany(2,["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>500]);

        VacationFactory::createOne(['employee' => $employee, 'type'=>$vacationType]);

        $this->call("/api/vacations",$user)->assertJsonMatches('"hydra:totalItems"',1);
    }


    private function call(string $url, $user, int $status=200,string $method = 'get')
    {
        return $this->browser()
            ->actingAs($user)
            ->$method($url)
            ->assertStatus($status);
    }

    public function testVacationLimit()
    {
        $employee = EmployeeFactory::createOne();
        $user = UserFactory::createOne(['employee' => $employee]);

        $vacationType = VacationTypesFactory::createOne();
        VacationLimitsFactory::createMany(2,["employee"=>$employee,'vacationType'=>$vacationType, 'daysLimit'=>10]);

        VacationStatusFactory::createOne(['name'=>"oczekujący"]);
        //VacationFactory::createOne([]);

         $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee' => '/api/employees/'.$employee->getId(),
                    'type' => '/api/vacation_types/'.$vacationType->getId(),
                    'dateFrom' =>'2023-08-30',
                    'dateTo'=>'2023-09-05'
                ]
                ])
            ->assertStatus(201);

        $this->browser()
            ->actingAs($user)
            ->post('/api/vacations',[
                'json'=>[
                    'employee' => '/api/employees/'.$employee->getId(),
                    'type' => '/api/vacation_types/'.$vacationType->getId(),
                    'dateFrom' =>'2023-08-31',
                    'dateTo'=>'2023-09-31'
                ]
            ])
            ->assertStatus(400);
    }
}