<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class UserStateProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly ProcessorInterface $innerProcessor,
        private readonly Security $security
    )
    {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if($data instanceof User) {
            if(!$this->security->isGranted('ROLE_ADMIN')){
                if($data->getId() == $this->security->getUser()->getId())
                {
                    throw new BadRequestException("Brak Uprawnień", 401);
                }

                if($context["previous_data"]->getRoles() != $data->getRoles()){
                    throw new BadRequestException("Brak Uprawnień", 401);
                }
            }
            if($operation instanceof Delete) {
                if (!empty($data->getEmployee())) {
                    throw new BadRequestException(
                        "Nie można usunąć użytkownika który ma przypisanego pracownika.", 400
                    );
                }
            }
        }

        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
