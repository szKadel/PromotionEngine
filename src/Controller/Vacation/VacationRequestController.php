<?php

namespace App\Controller\Vacation;

use App\Controller\Notification\EmailNotificationController;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationLimits;
use App\Repository\Security\UserRepository;
use App\Repository\Vacation\Settings\BankHolidayRepository;
use App\Repository\Vacation\VacationRepository;
use App\Service\Vacation\CounterVacationDays;
use App\Service\Vacation\WorkingDaysCounterService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class VacationRequestController
{

    private Vacation $vacation;

    public function __construct(
        private readonly VacationRepository $vacationRepository,
        private readonly LimitsVacationController $limitsVacationController,
        private readonly StatusVacationController $statusVacationController,
        private readonly CounterVacationDays $counterVacationDays,
        private readonly EmailNotificationController $emailNotificationController,
        private readonly Security $security,
        private readonly UserRepository $userRepository,
        private readonly BankHolidayRepository $bankHolidayRepository
    )
    {
    }

    private function setVacation(Vacation $vacation)
    {
        $this->vacation = $vacation;
    }

    private function setPreaviusVacation(Vacation $vacation)
    {
        $this->vacation = $vacation;
    }

    public function onVacationRequestPost(Vacation $vacation)
    {
        if($vacation->getEmployee()->getUnActive()){
            throw new BadRequestException("Wniosek tego pracownika jest dezaktywowany.");
        }

        $this -> setVacation($vacation);
        $this->setSpendVacationDays();
        $this -> checkDateAvailability()->checkInputData();
        $this -> checkCompany();
        $this -> checkVacationStatus();

        if($vacation->getSpendVacationDays() == 0){
            throw new BadRequestException('Wniosek nie może być wystawiony na 0 dni.');
        }

        $this -> checkVacationDaysLimit();
        $this -> checkReplacement();
        $this -> vacation -> setCreatedBy($this->userRepository->find($this->security->getUser()->getId()));
        $this -> vacation -> setCreatedAt(new \DateTime());
        $this -> emailNotificationController    ->  OnVacationAdd($vacation);
    }

    public function checkInputData()
    {
        if($this->vacation->getDateFrom() > $this->vacation->getDateTo()){
            throw new BadRequestException("Data rozpoczęcia musi być wcześniej niż zakończenia.", 400);
        }
    }

    public function checkCompany():void
    {
        if($this->vacation->getEmployee()?->getCompany()?->getId() == 5){
            throw new BadRequestException("Nie można utworzyć urlopu dla użytkownika z firmy zlecenie.", 400);
        }

    }

    public function onVacationUpdate(Vacation $vacation, Vacation $previousVacation)
    {
        $this   ->setVacation($vacation);
        $this   ->setPreaviusVacation($previousVacation);
        $this   ->checkVacationDaysLimit();
        $this   ->checkReplacement();
    }

    public function checkReplacement(): void
    {
        if(!empty($this->vacation->getReplacement())) {

            if($this->vacation->getEmployee()->getId() == $this->vacation->getReplacement()->getId() ){
                throw new BadRequestException("Osoba tworząca urlop nie może być jednocześnie osobą zastępującą.", 400);
            }

            $this->vacationRepository->findExistingVacationForUserInDateRange(
                $this->vacation->getReplacement(),
                $this->vacation->getDateFrom(),
                $this->vacation->getDateTo()
            );
        }
    }

    private function checkDateAvailability():self
    {
        $this->vacationRepository->findExistingVacationForUserInDateRange($this->vacation->getEmployee(),$this->vacation->getDateFrom(),$this->vacation->getDateTo());
        return $this;
    }

    private function checkVacationStatus():void
    {
        $this->vacation->setStatus($this->statusVacationController->setStatusForCreatedVacation($this->vacation));
    }

    private function checkVacationDaysLimit(): void
    {

        if ($this->vacation->getType()->getId() == 1 || $this->vacation->getType()->getId() == 11) {
            return;
        }

        $limitDays = $this->getVacationLimits()->getDaysLimit() + ($this->getVacationLimits()->getUnusedDaysFromPreviousYear() ?? 0);

        $spendDays = $this->counterVacationDays->countVacationSpendDays($this->vacation->getEmployee(),$this->vacation->getType(),$this->vacation->getDateFrom()->format('Y'));

        if($this->vacation->getSpendVacationDays() == 0){
            throw new BadRequestException('Wniosek nie może być wystawiony na 0 dni.');
        }

        if ($limitDays == 0) {
            return;
        }

        if ($limitDays < $spendDays + $this->vacation->getSpendVacationDays()) {
            throw new BadRequestException('Drogi Pracowniku! Wniosek nie może zostać utworzony z powodu przekroczenia limitu dostępnych dni wolnych.');
        }
    }

    public function setSpendVacationDays():void
    {
        $this->vacation->setSpendVacationDays(WorkingDaysCounterService::countWorkingDays($this->vacation->getDateFrom(),$this->vacation->getDateTo(),$this->bankHolidayRepository));
    }

    private function getVacationLimits():VacationLimits
    {
        return $this->limitsVacationController->getVacationLimit($this->vacation);
    }
}