<?php

namespace App\Factory\Vacation;

use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationLimits;
use App\Factory\Company\EmployeeFactory;
use App\Factory\VacationTypesFactory;
use App\Repository\Vacation\Settings\BankHolidayRepository;
use App\Repository\VacationRepository;
use App\Service\WorkingDaysCounterService;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Vacation>
 *
 * @method        Vacation|Proxy create(array|callable $attributes = [])
 * @method static Vacation|Proxy createOne(array $attributes = [])
 * @method static Vacation|Proxy find(object|array|mixed $criteria)
 * @method static Vacation|Proxy findOrCreate(array $attributes)
 * @method static Vacation|Proxy first(string $sortedField = 'id')
 * @method static Vacation|Proxy last(string $sortedField = 'id')
 * @method static Vacation|Proxy random(array $attributes = [])
 * @method static Vacation|Proxy randomOrCreate(array $attributes = [])
 * @method static VacationRepository|RepositoryProxy repository()
 * @method static Vacation[]|Proxy[] all()
 * @method static Vacation[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Vacation[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Vacation[]|Proxy[] findBy(array $attributes)
 * @method static Vacation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Vacation[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class VacationFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private BankHolidayRepository $bankHolidayRepository
    )
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        $vacation_type = VacationTypesFactory::createOne();

        $employee = EmployeeFactory::createOne();

        VacationLimitsFactory::createOne(['daysLimit'=>225,'employee'=>$employee,'vacationType'=>$vacation_type]);

        $dateFrom = self::faker()->dateTime()->setDate(2023,10,22);
        $dateTo = self::faker()->dateTimeThisMonth('+20 days');

        $spendVacationDays = WorkingDaysCounterService::countWorkingDays($dateFrom,$dateTo,$this->bankHolidayRepository);

        return [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'employee' => $employee,
            'SpendVacationDays' => $spendVacationDays,
            'status' => VacationStatusFactory::findOrCreate(['name' => 'Oczekujący']),
            'type' => $vacation_type
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Vacation $vacation): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Vacation::class;
    }
}
