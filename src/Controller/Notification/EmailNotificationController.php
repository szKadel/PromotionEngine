<?php

namespace App\Controller\Notification;

use App\Entity\Settings\Notification;
use App\Entity\Vacation\Vacation;
use App\Repository\Settings\NotificationRepository;
use App\Service\EmailService;

class EmailNotificationController
{
    private Notification | null $notification;

    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly EmailService $emailService
    )
    {
    }

    private function setNotificationsSettings(): void
    {
        $this->notification = $this->notificationRepository->getNotificationsSettings();
    }

    public function OnVacationAdd(Vacation $vacation):void
    {
        $this->setNotificationsSettings();

        if ($this->notification == null)
        {
            return;
        }

        if($this->notification ->isNotificateAdminOnVacationRequest()) {
            $this->emailService -> sendNotificationEmailToAllAdmin($vacation);
        }

        if($this->notification ->isNotificateDepartmentModOnCreatedVacation())
        {
            $this->emailService->sendNotificationToModofDepartment($vacation);
        }
    }
}