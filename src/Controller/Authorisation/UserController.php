<?php

namespace App\Controller\Authorisation;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController
{
    public function __construct(
        private UserRepository $userRepository
    )
    {
    }


    #[IsGranted('ROLE_ADMIN')]
    public function deleteEmployee(int $id):void
    {
        $user = $this->userRepository->find($id);
    }
}