<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Controller\Vacation\VacationRequestController;
use App\Entity\User;
use App\Entity\Vacation\Vacation;
use App\Entity\Vacation\VacationLimits;
use App\Repository\EmployeeVacationLimitRepository;
use App\Repository\Settings\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\VacationRepository;
use App\Repository\VacationStatusRepository;
use App\Service\EmailService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class VacationStateProcessor implements ProcessorInterface
{
    public function __construct(
        private VacationRequestController $vacationRequestController,
        private ProcessorInterface $innerProcessor,
        private Security $security,
        private VacationRepository $vacationRepository,
        private EmployeeVacationLimitRepository $employeeVacationLimitRepository,
        private EmailService $emailService,
        private UserRepository $userRepository,
        private NotificationRepository $notificationRepository,
        private VacationStatusRepository $vacationStatusRepository
    )
    {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): void
    {
        if($data instanceof Vacation) {
            if($operation instanceof Post) {
                $this->vacationRequestController->onVacationRequestPost($data);

            } elseif ($operation instanceof Put) {

                if($data->getEmployee()?->getUnActive()){
                    throw new BadRequestException("Pracownika dla tego wniosku jest dezaktywowany.");
                }

                if($data->getReplacement()?->getUnActive()){
                    throw new BadRequestException("Pracownik na zastępstwie jest dezaktywowany.");
                }

                if ($data->getType()->getId() != 1 || $data->getType()->getId() != 11) {
                    $this->checkVacationLimits($data);

                }

                if($data->getStatus() != $context["previous_data"]->getStatus())
                {
                    if($data->getStatus()->getName() == "Potwierdzony") {


                        if($data->getType()->getName() == "Inny" || $data->getType()->getName() == "Plan Urlopowy")
                        {
                            throw new BadRequestException('Drogi Moderatorze! Nie możesz zaakceptować wniosku "Inne" wybierz rodzaj wniosku zawarty w komentarzu Pracownika');
                        }

                        $data->setAcceptedAt(new \DateTimeImmutable());

                        $user = $this->security->getUser();

                        $data->setAcceptedBy($this->userRepository->find($user->getId()));

                        if ($this->notificationRepository -> getNotificationsSettings() ?->isNotificateReplacementUser() && !empty($data->getReplacement())) {
                            $this->emailService -> sendReplacementEmployeeNotification($data);
                        }
                    }

                    if($data->getStatus()->getName() == "Odrzucony") {
                        if($this -> security -> isGranted('ROLE_KADR')){
                            throw new BadRequestException('Brak Uprawnień');
                        }

                        $user = $this->security->getUser();
                        $data->setRejectBy($this->userRepository->find($user->getId()));
                    }

                    if ($this->notificationRepository -> getNotificationsSettings() ?-> isNotificateUserOnVacationRequestAccept()) {
                        $this->emailService -> sendNotificationToOwnerOnChangeStatus($data);
                    }
                }

                if ($context["previous_data"]->getStatus()->getName() == "Potwierdzony" && $data->getStatus()->getName() == "Anulowany")
                {
                    if($this -> security -> isGranted('ROLE_KADR')){
                        throw new BadRequestException('Brak Uprawnień');
                    }

                    $date = date('Y-m-d');
                    if($this->security->getUser()->getId() == $data->getEmployee()->getUser()->getId() ??"" && $date <= $data->getDateFrom()) {
                        $user = $this->security->getUser();

                        $data->setAnnulledAt(new \DateTimeImmutable());

                        $data->setAnnulledBy($this->userRepository->find($user->getId()));
                    }

                    if($this->security->isGranted("ROLE_ADMIN")&& $date <= $data->getDateTo()) {
                        $user = $this->security->getUser();

                        $data->setAnnulledAt(new \DateTimeImmutable());

                        if ($user instanceof User) {
                            $data->setAnnulledBy($user);
                        }
                    }
                }
                if ($data->getType() != $context["previous_data"]->getType())
                {
                    if($context["previous_data"]->getType()->getName() == "Plan urlopowy") {
                        $data->setStatus($this->vacationStatusRepository->findByName("Oczekujący"));
                    }

                    if($context["previous_data"]->getType()->getName() == "Oczekujący" && $data->getType()->getName() == 'Plan urlopowy') {
                        $data->setStatus($this->vacationStatusRepository->findByName("Zaplanowany"));
                    }
                }


            $data->setSpendVacationDays();

            if($data->getSpendVacationDays() == 0){
                throw new BadRequestException('Wniosek nie może być wystawiony na 0 dni.');
            }

            }
        }

        if($data instanceof VacationLimits)
        {
            if($operation instanceof Post) {
                if ($this->employeeVacationLimitRepository->findTypeForEmployee(
                        $data->getEmployee(),
                        $data->getVacationType()
                    ) !== null) {
                    throw new BadRequestException("Limit został już dodany!", 400);
                }
            }
        }
        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function checkVacationLimits(Vacation $vacation)
    {
        $vacationUsedInDays = $this->vacationRepository->findVacationUsedByUser(
            $vacation->getEmployee(),
            $vacation->getType()
        );

        $limit = $this->employeeVacationLimitRepository->findLimitByTypes(
            $vacation->getEmployee(),
            $vacation->getType()
        );

        if (empty($limit[0])) {
            throw new BadRequestException('Ten Urlop nie został przypisany dla tego użytkownika.');
        }

        $limitDays = $limit[0]->getDaysLimit() + ($limit[0]->getUnusedDaysFromPreviousYear() ?? 0);

        if ($limitDays != 0) {
            if ($limitDays < $vacationUsedInDays + $vacation->getSpendVacationDays()) {
                throw new BadRequestException('Uwaga! Nie możesz zaakceptować tego wniosku. Limit na ten rok dla tego użytkownika został wykorzystany.');
            }
        }

    }
}
