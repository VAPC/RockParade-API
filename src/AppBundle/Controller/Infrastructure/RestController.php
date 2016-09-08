<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Entity\Infrasctucture\AbstractRepository;
use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Response\ApiError;
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
    protected function createCompleteCollectionResponse(AbstractRepository $repository, $limit, $offset): CollectionApiResponse
    {
        $limit = (int) filter_var($limit, FILTER_VALIDATE_INT);
        $offset = (int) filter_var($offset, FILTER_VALIDATE_INT);

        $entities = $repository->findAllWithLimitAndOffset($limit, $offset);
        $entitiesQuantity = $repository->countAll();

        $response = new CollectionApiResponse($entities, Response::HTTP_OK, $entitiesQuantity, $limit, $offset);

        return $response;
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
}
