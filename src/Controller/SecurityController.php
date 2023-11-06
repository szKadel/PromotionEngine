<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Controller\Authorisation\ApiTokenController;
use App\Entity\ApiToken;
use App\Entity\User;
use MongoDB\Driver\Exception\AuthenticationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

        if($user -> getValidApiToken() == null)
        {


            $token = new ApiToken();
            $user->addApiToken($token);
            $apiTokenController -> add($token);
        }else{
            $apiTokens = $user->getApiTokens()->toArray();
            if(count($apiTokens) > 0) {
                foreach ($apiTokens as $apiToken) {
                    $user->removeApiToken($apiToken);
                    if(!$apiToken->isValid()) {
                        $apiTokenController->delete($apiToken);
                    }else{
                        $token = $apiToken;
                    }
                }
            }
        }

        $response = [
            'token' => $token->getToken(),
            'user'=>$user->getId(),
            'expiredAt' => new ($token->getExpiresAt())
        ];

        return new JsonResponse($response,200);
    }

    #[Route('/logout',name: 'app_logout')]
    public function logout():void
    {

    }

    #[Route('/api/getCurrentUser/', name: 'app_check_user', methods: ['GET'])]
    public function getCurrentUser(IriConverterInterface $iriConverter, #[CurrentUser] User $user ):Response
    {
        if(empty($user->getId()) || $user === null)
        {
            return new JsonResponse([
                'error' => 'User dont found'
            ]);
        }

        if(!empty($user->getEmployee())) {

            $employee = [
                    '@id' => $iriConverter->getIriFromResource($user->getEmployee()) ?? "",
                    'id' => $user->getEmployee()?->getId(),
                    'name' => $user->getEmployee()->getName()??"",
                    'surname' => $user->getEmployee()->getSurname() ?? "",
                    'department' => [
                        '@id' => $iriConverter->getIriFromResource($user->getEmployee()->getDepartment()) ?? "",
                        'id' => $user->getEmployee()->getDepartment()->getId() ?? "",
                        'name' => $user->getEmployee()->getDepartment()->getName() ?? ""
                    ]
                ] ?? null;

            $extendedAccess = $user->getEmployee()->getEmployeeExtendedAccesses();
            if(!empty($extendedAccess)) {
                foreach ($extendedAccess as $access) {
                    $employee["employeeExtendedAccesses"] = [
                        'department' => [
                            '@id' => $iriConverter->getIriFromResource($access->getDepartment()) ?? "",
                            'id' => $access->getDepartment()->getId() ?? "",
                            'name' => $access->getDepartment()->getName() ?? ""
                        ]
                    ];
                }
            }
        }

        return new JsonResponse([
                'id' =>$user->getId(),
                'email'=>$user->getEmail(),
                'roles'=>$user->getRoles(),
                'userName' => $user->getUsername(),
                'employee' => $employee ?? null
            ]);
    }

    #[Route('/user/changePassword',name: 'app_logout', methods: ['POST'])]
    public function updatePassword(#[CurrentUser] User $user, UserPasswordHasherInterface $userPasswordHasher, string $oldPassword, string $newPassword)
    {
        if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)) {
            $user->setPlainPassword($newPassword);

            if($user->getPlainPassword())
            {
                $user->setPassword($userPasswordHasher->hashPassword($user,$user->getPlainPassword()));
                return true;
            }
        }else{
            throw new AuthenticationException("Aktualne hasło jest niepoprawne.");
        }
    }


}