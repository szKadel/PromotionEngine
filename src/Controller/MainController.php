<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route('/',name: 'app_main_mainpage')]
    public function mainPage():void
    {
        http_redirect("https://solarstudiohr.beupsoft.pl/view/");
    }
}