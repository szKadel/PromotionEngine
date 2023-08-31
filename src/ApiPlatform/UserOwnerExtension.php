<?php

namespace App\ApiPlatform;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Company\Employee;
use App\Entity\Vacation\Vacation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserOwnerExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Vacation::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
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
}