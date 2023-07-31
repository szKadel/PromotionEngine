<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\LazyResponseException;

class BitrixController
{
    #[Route('/bitrix/handler')]
    public function getHandler(): RedirectResponse | JsonResponse
    {
        try {
            $domain =    $_REQUEST["DOMAIN"]    ?? throw new Exception('Bad Auth', 403);
            $member_id = $_REQUEST["member_id"] ?? throw new Exception('Bad Auth', 403);

            return new RedirectResponse('https://github.beupsoft.pl/BeUpHR/home?domain='. $domain . '&member_id=' . $member_id);
        } catch (Exception $e) {
            return new JsonResponse([
                "message" => $e->getMessage()
            ],
                $e->getCode());
        }
    }

    #[Route('/bitrix/install')]
    public function install(): JsonResponse
    {
        return new JsonResponse("Instalacja");
    }

    #[Route('/bitrix/install')]
    private function mergeDepartments(): JsonResponse
    {
        return new JsonResponse("Instalacja");
    }

    #[Route('/bitrix/install')]
    private function mergeEmployee(): JsonResponse
    {
        return new JsonResponse("Instalacja");
    }
}