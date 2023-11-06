<?php

namespace App\ApiPlatform;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Company\Employee;
use App\Entity\EmployeeExtendedAccesses;
use App\Entity\Vacation\Vacation;
use App\Repository\EmployeeExtendedAccessesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserOwnerExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(private readonly Security $security, private EmployeeExtendedAccessesRepository $extendedAccessesRepository)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
        $this->groupModerator($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
        $this->groupModerator($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Vacation::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') ||$this->security->isGranted('ROLE_KADR') || $this->security->isGranted('ROLE_MOD') || null === $user = $this->security->getUser()) {
            return;
        }

        if(empty($user->getEmployee()->getId())){
            throw new CustomUserMessageAccountStatusException("Konto pracownicze nie zostało jeszcze przypisane do tego użytkownika.");
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->join(sprintf('%s.employee', $rootAlias), 'u');
        $queryBuilder->andWhere('u.id = :current_user_id');
        $queryBuilder->setParameter('current_user_id', $user->getEmployee()->getId());
    }

    public function groupModerator(QueryBuilder $queryBuilder, string $resourceClass) {
        if ($resourceClass !== Vacation::class || $this->hasSufficientPermissions() || !$user = $this->security->getUser()) {
            return;
        }

        if ($this->security->isGranted('ROLE_MOD')) {
            $this->applyDepartmentFilters($queryBuilder);
        }
    }

    private function hasSufficientPermissions() {
        return $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_KADR');
    }

    private function applyDepartmentFilters(QueryBuilder $queryBuilder) {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join(sprintf('%s.employee', $rootAlias), 'u');


        $userEmployee = $this->security->getUser()->getEmployee();

        $accessDepartments = $this->getExtendedAccess($userEmployee);

        if(!empty($accessDepartments)) {
            $departmentIds = array_map(function ($departmentAccess) {
                return $departmentAccess->getDepartment()->getId();
            }, $accessDepartments);

            $departmentIds[] = $userEmployee->getDepartment()->getId();

            $queryBuilder
                ->andWhere('u.department IN (:departmentIds)')
                ->setParameter('departmentIds', $departmentIds);
        }
    }

    private function getExtendedAccess($employee): array
    {
        return $this->extendedAccessesRepository->findBy(['employee' => $employee]);
    }
}