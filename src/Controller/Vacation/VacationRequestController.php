<?php

namespace App\Controller\Vacation;

use App\Controller\Notification\EmailNotificationController;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationLimits;
use App\Repository\UserRepository;
use App\Repository\VacationRepository;
use App\Service\Vacation\CounterVacationDays;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class VacationRequestController
{

    private Vacation $vacation;

    public function __construct(
        private VacationRepository $vacationRepository,
        private LimitsVacationController $limitsVacationController,
        private StatusVacationController $statusVacationController,
        private CounterVacationDays $counterVacationDays,
        private EmailNotificationController $emailNotificationController,
        private Security $security,
        private UserRepository $userRepository
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

        if($vacation->getReplacement()?->getUnActive()){
            throw new BadRequestException("Pracownik na zastępstwie jest dezaktywowany.");
        }

        $this -> setVacation($vacation);
        $this -> checkDateAvailability()->checkInputData();
        $this -> checkCompany();
        $this -> checkVacationStatus();
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

        $spendDays = $this->counterVacationDays->getVacationDaysSpend($this->vacation->getEmployee(),$this->vacation->getType());

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

    private function getVacationLimits():VacationLimits
    {
        return $this->limitsVacationController->getVacationLimit($this->vacation);
    }


}