<?php

namespace AppBundle\Service\Entity;

use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Response\ApiError;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
abstract class EntityService
{

    /**
     * @param string $entityFullName Entity class name
     * @param string|int $id Entity id
     * @return ApiError
     * @throws EntityNotFoundException
     */
    public function createEntityNotFoundResponse(string $entityFullName, $id): ApiError
    {
        $entityName = (new \ReflectionClass($entityFullName))->getShortName();

        return new ApiError(
            sprintf('%s "%s" was not found.', $entityName, $id),
            Response::HTTP_NOT_FOUND
        );
    }
}
