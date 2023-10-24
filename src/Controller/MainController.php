<?php

namespace App\Controller;

use Doctrine\DBAL\Schema\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment;

class MainController extends AbstractController
{
    public function __construct(
        private Environment $twig
    )
    {

    }

    #[Route('/',name: 'app_main_mainpage')]
    public function mainPage(): RedirectResponse
    {
        return new RedirectResponse('/view/');
    }

    #[Route('/test')]
    public function test(): \Symfony\Component\HttpFoundation\Response
    {
        return  $this->render("email/notification/adminNewVacation.html.twig");
    }
}