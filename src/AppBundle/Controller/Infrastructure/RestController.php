<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Entity\Infrasctucture\AbstractRepository;
use AppBundle\Response\CollectionApiResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Response\Infrastructure\HttpLocationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class RestController extends Controller
{

    const TYPE_JSON = 'application/json';

    public function respond(AbstractApiResponse $apiResponse): Response
    {
        $response = $this->get('rockparade.response_factory')->createResponse($apiResponse);

        $this->setLocation($response, $apiResponse);
        $this->setJsonContentType($response);

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
        $clearMissing = $request->getMethod() != 'PATCH';

        $form->submit($formData, $clearMissing);
    }

    /**
     * @return string[]
     */
    protected function getFormErrors(FormInterface $form): array
    {
        /** @var string[] $errors */
        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            $parametersString = join(',', $error->getMessageParameters());
            if (!$parametersString || $parametersString === 'null') {
                $errors[] = $error->getMessage();
            } else {
                $errors[] = sprintf('%s - %s', $parametersString, $error->getMessage());
            }
        }

        return $errors;
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
    protected function createCollectionResponse(AbstractRepository $repository, $limit, $offset): CollectionApiResponse
    {
        $limit = (int) filter_var($limit, FILTER_VALIDATE_INT);
        $offset = (int) filter_var($offset, FILTER_VALIDATE_INT);

        $entities = $repository->findAllWithLimitAndOffset($limit, $offset);
        $entitiesQuantity = $repository->countAll();

        $response = new CollectionApiResponse($entities, Response::HTTP_OK, $entitiesQuantity, $limit, $offset);

        return $response;
    }

    private function setLocation(Response $response, AbstractApiResponse $apiResponse)
    {
        if ($apiResponse instanceof HttpLocationInterface) {
            $response->headers->set('Location', $apiResponse->getLocation());
        }
    }

    private function setJsonContentType(Response $response)
    {
        $response->headers->set('Content-Type', self::TYPE_JSON);
    }
}
