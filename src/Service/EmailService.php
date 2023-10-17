<?php

namespace App\Service;

use App\Entity\Company\Employee;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{

    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository)
    {

    }

    public function sendEmail(string $subject, string $to, string $body): void
    {
        $email = (new Email())
            ->from('beuphr@beupsoft.pl')
            ->to($to)
            ->subject($subject)
            ->html($body);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {

        }
    }

    public function sendNotificationEmailToAllAdmin(Employee $employee): void
    {
        $admins = $this->userRepository->getAdmins();
        foreach ($admins as $admin) {
            if(!empty($admin?->getEmail())) {
                $this->sendEmail(
                    "HHG - powiadomienie",
                    $admin->getEmail(),
                    "Użytkownik " . $employee->getName() . " " . $employee->getSurname(
                    ) . " utworzył wniosek urlopowy, który oczekuje na Twoją akceptację."
                );
            }
        }
    }

    public function sendReplacementEmployeeNotification(Employee $employee, Employee $replacementUser, $dateFrom, $dateTo):void
    {
        if (!empty($replacementUser->getUser()?->getEmail())) {
            $this->sendEmail(
                "HHG - powiadomienie",
                $replacementUser->getUser()->getEmail(),
                "Zostałeś przypisany jako zastępstwo za użytkownika " . $employee->getName() . " " . $employee->getSurname()." w terminie od ".$dateFrom." do ".$dateTo
            );
        }
    }

    public function sendNotificationToModofDepartment(Employee $employee):void
    {
        $mods = $this->userRepository->getModerators($employee->getDepartment());
        foreach ($mods as $mod){
            if(!empty($mod?->getEmail())) {
                $this->sendEmail(
                    "HHG - powiadomienie",
                    $mod->getEmail(),
                    "Użytkownik " . $employee->getName() . " " . $employee->getSurname(
                    ) . " utworzył wniosek urlopowy, który oczekuje na Twoją akceptację."
                );
            }
        }
    }

    public function sendNotificationToOwnerOnAccept(Employee $employee):void
    {
        if(!empty($employee->getUser()?->getEmail())) {
            $this->sendEmail(
                "HHG - powiadomienie",
                $employee->getUser()->getEmail(),
                "Twój wniosek urlopowy został zaakceptowany."
            );
        }
    }
}