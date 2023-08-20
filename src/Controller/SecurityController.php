<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Controller\Authorisation\ApiTokenController;
use App\Entity\ApiToken;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(IriConverterInterface $iriConverter,#[CurrentUser] User $user = null, ApiTokenController $apiTokenController) :Response
    {
        if($user === null ){
            throw new UnauthorizedHttpException("");
        }

        if(!$user -> getApiTokens()->get(0)?->isValid()){
            if(!empty($user -> getApiTokens()->get(0))) {
                $apiTokenController->delete($user->getApiTokens()->get(0));
            }
            $token = new ApiToken();
            $token ->setOwnedBy($user);
            $apiTokenController -> add($token);
        }

        $response = [
            'token' => $user -> getApiTokens()->get(0)?->getToken() ?? $token->getToken()
        ];


        return new JsonResponse($response,200);
    }

    #[Route('/logout',name: 'app_logout')]
    public function logout():void
    {
    }

}