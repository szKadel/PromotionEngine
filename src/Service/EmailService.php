<?php

namespace App\Service;

use App\Entity\Security\User;
use App\Entity\Vacation\Vacation;
use App\Repository\Security\UserRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailService
{

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepository $userRepository,
        private readonly Environment $twig)
    {

    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function sendEmail(string $subject, string $to, string $templateName, Vacation $vacationRequest): void
    {
        $body = $this->twig->render("email/notification/".$templateName, ['vacation'=>$vacationRequest]);

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

    public function sendResetPassword(string $subject, string $to, string $templateName, User $user, string $password): void
    {
        $body = $this->twig->render("email/changePassword/".$templateName, ['user'=>$user, 'password'=>$password]);

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

    public function sendNotificationEmailToAllAdmin(Vacation $vacation): void
    {
        $admins = $this->userRepository->getAdmins();
        foreach ($admins as $admin) {
            if(!empty($admin?->getEmail())) {
                $this->sendEmail(
                    "HHG - powiadomienie",
                    $admin->getEmail(),
                    "adminNewVacation.html.twig",
                    $vacation
                );
            }
        }
    }

    public function sendReplacementEmployeeNotification(Vacation $vacation):void
    {
        if (!empty($vacation->getReplacement()?->getUser()?->getEmail())) {
            $this->sendEmail(
                "HHG - powiadomienie",
                $vacation->getReplacement()->getUser()->getEmail(),
                "replacementNewVacation.html.twig",$vacation);
        }
    }

    public function sendNotificationToModofDepartment(Vacation $vacation):void
    {
        $mods = $this->userRepository->getModerators($vacation->getEmployee()->getDepartment());
        foreach ($mods as $mod){
            if(!empty($mod?->getEmail())) {
                $this->sendEmail(
                    "HHG - powiadomienie",
                    $mod->getEmail(),
                    "modNewVacation.html.twig",$vacation);
            }
        }

        $extMods = $vacation->getEmployee()->getDepartment()->getEmployeeExtendedAccesses();
        foreach ($extMods as $extMod){
            if(!empty($extMod->getEmployee()->getUser()?->getEmail() )) {
                $this->sendEmail(
                    "HHG - powiadomienie",
                    $extMod->getEmployee()->getUser()?->getEmail(),
                    "modNewVacation.html.twig",$vacation);
            }
        }
    }

    public function sendNotificationToOwnerOnChangeStatus(Vacation $vacation):void
    {
        if(!empty($vacation->getEmployee()?->getUser()?->getEmail())) {
            $this->sendEmail(
                "HHG - powiadomienie",
                $vacation->getEmployee()->getUser()->getEmail(),
                "employeeStatusChange.html.twig",$vacation);
        }
    }

    public function sendNotificationToOwnerOnCreate(Vacation $vacation):void
    {
        if(!empty($vacation->getEmployee()->getUser()?->getEmail())) {
            $this->sendEmail(
                "HHG - powiadomienie",
                $vacation->getEmployee()->getUser()->getEmail(),
                "employeeNewRequest.html.twig",$vacation);
        }
    }
}