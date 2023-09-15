<?php

namespace App\Controller;

use App\Service\BitrixService;
use Symfony\Component\Routing\Annotation\Route;

class MigrationController
{
    #[Route('/bitrix/migration/users')]
    public function migrateUsers(BitrixService $bitrixService)
    {
        $result = $bitrixService->call('method.get',[]);
    }

    public function migrateDepartments()
    {

    }
}