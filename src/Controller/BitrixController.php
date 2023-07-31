<?php

namespace App\Controller;

use App\Service\Bitrix\CRest;
use App\Service\Bitrix\Instance;
use Doctrine\DBAL\Schema\View;
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

            return new RedirectResponse('https://github.beupsoft.pl/BeUpHR/home?domain='. $domain??'undefined' . '&member_id=' . $member_id??'undefined');
        } catch (Exception $e) {
            return new JsonResponse([
                "message" => $e->getMessage()
            ],
                $e->getCode());
        }
    }

    #[Route('/bitrix/install')]
    public function install(): View
    {
            $result = CRest::installApp();

            if ($result['rest_only'] === false) {
                return new View("install.html.twig","");
            }
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