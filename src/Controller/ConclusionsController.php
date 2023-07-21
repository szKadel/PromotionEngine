<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConclusionsController extends AbstractController
{
    #[Route('api/conclusions')]
    public function index(): JsonResponse
    {
        $result = [
            'name' => 'test'
        ];

        return new JsonResponse($result);
    }
}