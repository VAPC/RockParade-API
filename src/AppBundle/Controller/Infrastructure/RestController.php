<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Entity\Infrasctucture\AbstractRepository;
use AppBundle\Enum\ApiOperation;
use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResponse;
use AppBundle\Response\CollectionApiResponse;
use AppBundle\Response\FileResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Response\Infrastructure\HttpLocationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class RestController extends Controller
{

    const MIME_TYPE_JSON = 'application/json';

    public function respond(AbstractApiResponse $apiResponse): Response
    {
        $response = $this->get('rockparade.http_response_factory')->createResponse($apiResponse);

        $this->setLocation($response, $apiResponse);
        $this->setContentType($apiResponse, $response);

        return $response;
    }

    protected function createAndProcessForm(
        Request $request,
        string $type,
        $data = null,
        $options = []
    ) {
        $form = parent::createForm($type, $data, $options);
        $this->processForm($request, $form);

        return $form;
    }

    /**
     * Process form
     * @param Request $request
     * @param FormInterface $form
     */
    protected function processForm(Request $request, FormInterface $form)
    {
        $formData = json_decode($request->getContent(), true) ?? $request->request->all();
        $clearMissing = $request->getMethod() != Request::METHOD_PATCH;

        $form->submit($formData, $clearMissing);
    }

    /** {@inheritDoc} */
    protected function createFormBuilder($data = null, array $options = []): FormBuilder
    {
        $options['allow_extra_fields'] = true;

        return parent::createFormBuilder($data, $options);
    }

    /**
     * Create collection api response with total, limit and offset parameters
     * @param integer|null $limit
     * @param integer|null $offset
     */
    protected function listEntities(AbstractRepository $repository, $limit, $offset): Response
    {
        $limit = (int) filter_var($limit, FILTER_VALIDATE_INT);
        $offset = (int) filter_var($offset, FILTER_VALIDATE_INT);

        $entities = $repository->findAllWithLimitAndOffset($limit, $offset);
        $entitiesQuantity = $repository->countAll();

        $response = new CollectionApiResponse($entities, Response::HTTP_OK, $entitiesQuantity, $limit, $offset);

        return $this->respond($response);
    }

    /**
     * @param string $entityFullName Entity class name
     * @param string|int $id Entity id
     * @return ApiError
     * @throws EntityNotFoundException
     */
    protected function createEntityNotFoundResponse(string $entityFullName, $id): ApiError
    {
        return $this->get('rockparade.entity_service')->createEntityNotFoundResponse($entityFullName, $id);
    }

    /**
     * @param string|int $id
     */
    protected function viewEntity(AbstractRepository $repository, $id): Response
    {
        $entity = $repository->findOneById($id);
        $entityClass = $repository->getClassName();

        if ($entity) {
            $response = new ApiResponse($entity, Response::HTTP_OK);
        } else {
            $response = $this->createEntityNotFoundResponse($entityClass, $id);
        }

        return $this->respond($response);
    }

    private function setLocation(Response $response, AbstractApiResponse $apiResponse)
    {
        if ($apiResponse instanceof HttpLocationInterface) {
            $response->headers->set('Location', $apiResponse->getLocation());
        }
    }

    private function setContentType(AbstractApiResponse $apiResponse, Response $response)
    {
        if (!$apiResponse instanceof FileResponse) {
            $response->headers->set('Content-Type', self::MIME_TYPE_JSON);
        }
    }

    /**
     * Create ApiOperation from Request method
     */
    protected function createApiOperation(Request $request): ApiOperation
    {
        return new ApiOperation($request->getMethod());
    }
}
