<?php

namespace App\ApiPlatform;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserOwnerExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(private readonly Security $security, private IriConverterInterface $iriConverter)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
        $this->kadrWhere($queryBuilder, $resourceClass);
        $this->groupModerator($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Vacation::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_MOD') || $this->security->isGranted('ROLE_KADR') || null === $user = $this->security->getUser()) {
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

    public function groupModerator(QueryBuilder $queryBuilder, string $resourceClass)
    {
        if (Vacation::class !== $resourceClass || !$this->security->isGranted('ROLE_MOD') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->join(sprintf('%s.employee', $rootAlias), 'u');
        $queryBuilder->andWhere('u.department = :department');
        $queryBuilder->setParameter('department', $user->getEmployee()->getDepartment());

        $extendedAccess = $user->getEmployee()->getEmployeeExtendedAccesses();
        if(!empty($extendedAccess)) {
            foreach ($extendedAccess as $employeeExtendedAccesses) {
                $key = "department" . $employeeExtendedAccesses->getId();
                $queryBuilder->orWhere('u.department = :' . $key);
                $queryBuilder->setParameter($key, $employeeExtendedAccesses->getDepartment());
            }
        }
    }

    public function kadrWhere($queryBuilder, $resourceClass)
    {
        if(!$this->security->isGranted('ROLE_KADR') || Employee::class !== $resourceClass  ) {
            return;
        }

        $company = $this->iriConverter->getResourceFromIri("/api/companies/5");

        $queryBuilder->andWhere('o.company != :Zlecenia');
        $queryBuilder->setParameter('Zlecenia', $company);

    }
}