<?php

namespace App\Controller;

use App\Service\Bitrix\CRest;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\LazyResponseException;

class BitrixController extends AbstractController
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
    public function install(): \Symfony\Component\HttpFoundation\Response | JsonResponse
    {
            $result = CRest::installApp();

            if ($result['rest_only'] === false) {
                return $this->render("install.html.twig",["result"=>$result]);
            }

            return new JsonResponse(["Instalation Faild"]);
    }

    #[Route('/bitrix/department')]
    public function mergeDepartments(): JsonResponse
    {
        $result = CRest::call('user.get',[]);
        return  new JsonResponse($result);
    }

    #[Route('/bitrix/employee')]
    public function mergeEmployee(): JsonResponse
    {
        $result = CRest::call('user.get',[]);
        return  new JsonResponse($result);
    }
}