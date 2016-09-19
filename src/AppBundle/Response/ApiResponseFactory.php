<?php

namespace AppBundle\Response;

use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use AppBundle\Enum\ApiOperation;
use AppBundle\Exception\MethodNotImplemented;
use AppBundle\Exception\UnsupportedApiOperation;
use AppBundle\Exception\UnsupportedTypeException;
use AppBundle\Form\AbstractFormType;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Service\Entity\EntityService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Routing\Router;

/**
 * @author Vehsamrak
 */
class ApiResponseFactory
{

    /** @var string */
    private $filePath;

    /** @var EntityManager */
    private $entityManager;

    /** @var EntityService */
    private $entityService;

    /** @var Router */
    private $router;

    public function __construct(
        string $applicationRootPath,
        EntityManager $entityManager,
        EntityService $entityService,
        Router $router
    )
    {
        $this->filePath = realpath($applicationRootPath . '/../var/upload');
        $this->entityManager = $entityManager;
        $this->entityService = $entityService;
        $this->router = $router;
    }

    /**
     * Api response factory method
     * @throws UnsupportedApiOperation
     */
    public function createResponse(
        ApiOperation $operation,
        FormInterface $form,
        User $creator
    ): AbstractApiResponse
    {
        if (!$form->isValid()) {
            return new ApiValidationError($form);
        }

        if ($operation->getValue() === ApiOperation::CREATE) {
            return $this->processCreation($form, $creator);
        }

        throw new UnsupportedApiOperation($operation);
    }

    /**
     * @deprecated
     * @throws UnsupportedTypeException
     */
    public function createImageResponse($responseData): FileResponse
    {
        if ($responseData instanceof Image) {
            $imagesBasePath = $this->filePath . '/images/';
            $imagePath = $imagesBasePath . $responseData->getName();
            $response = new FileResponse($imagePath);
        } else {
            throw new UnsupportedTypeException();
        }

        return $response;
    }

    public function createNotFoundResponse(): ApiError
    {
        return new ApiError('Resource was not found.', Response::HTTP_NOT_FOUND);
    }

    private function processCreation(
        FormInterface $form,
        User $creator
    ): AbstractApiResponse
    {
        /** @var AbstractFormType $formData */
        $formData = $form->getData();
        $entityClass = $formData->getEntityClassName();

        $entity = $this->entityService->createEntityByFormData($formData, $creator, $entityClass);

        if (method_exists($entity, 'getId')) {
            $location = $this->createEntityHttpLocation($entity);
            $response = new CreatedApiResponse($location);
        } else {
            $response = new EmptyApiResponse(Response::HTTP_CREATED);
        }

        return $response;
    }

    /**
     * @param object $entity Entity
     * @param string $id Entity id
     * @return string
     */
    private function createEntityHttpLocation($entity): string
    {
        if (method_exists($entity, 'getId')) {
            $id = $entity->getId();
        } else {
            throw new MethodNotImplemented('getId');
        }

        $entityShortName = (new \ReflectionClass($entity))->getShortName();
        $route = strtolower($entityShortName) . '_view';

        return $this->router->generate($route, ['id' => $id], Router::ABSOLUTE_URL);
    }
}
