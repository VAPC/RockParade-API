<?php

namespace AppBundle\Service\Entity;

use AppBundle\Entity\Infrasctucture\Ambassador;
use AppBundle\Entity\Infrasctucture\AmbassadorMember;
use AppBundle\Entity\User;
use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Exception\MethodNotImplemented;
use AppBundle\Form\AbstractFormType;
use AppBundle\Response\ApiError;
use AppBundle\Service\Ambassador\AmbassadorService;
use AppBundle\Service\Entity\Infrastructure\EntityCreatorInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class EntityService
{

    /** @var AmbassadorService */
    private $ambassadorService;

    public function __construct(AmbassadorService $ambassadorService)
    {
        $this->ambassadorService = $ambassadorService;
    }

    /**
     * @param string $entityClassName Entity class name
     * @param string|int $id Entity id
     * @return ApiError
     * @throws EntityNotFoundException
     */
    public function createEntityNotFoundResponse(string $entityClassName, $id): ApiError
    {
        $entityName = (new \ReflectionClass($entityClassName))->getShortName();

        return new ApiError(
            sprintf('%s "%s" was not found.', $entityName, $id),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @return object Entity
     */
    public function createEntityByFormData(AbstractFormType $formType, User $creator, string $entityClass)
    {
        $service = $this->getServiceByEntity($entityClass);

        if (!$service instanceof EntityCreatorInterface) {
            throw new MethodNotImplemented(__METHOD__);
        }

        $entity = $service->createEntityByFormData($formType, $creator, $entityClass);

        return $entity;
    }

    private function getServiceByEntity(string $entityClass)
    {
        $entityClass = new \ReflectionClass($entityClass);

        if ($entityClass->isSubclassOf(Ambassador::class) || $entityClass->isSubclassOf(AmbassadorMember::class)) {
        	return $this->ambassadorService;
        } else {
            throw new MethodNotImplemented(__METHOD__);
        }
    }
}
