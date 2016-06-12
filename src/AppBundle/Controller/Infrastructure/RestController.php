<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Response\EmptyApiResponse;
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

    const FORMAT_JSON = 'json';

    const TYPE_JSON = 'application/json';

    public function respond(AbstractApiResponse $apiResponse): Response
    {
        $serializedData = $this->serialize($apiResponse);

        $response = new Response($serializedData, $apiResponse->getHttpCode());
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
     * @param FormInterface $form
     * @return string[]
     */
    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    /** {@inheritDoc} */
    protected function createFormBuilder($data = null, array $options = []): FormBuilder
    {
        $options['allow_extra_fields'] = true;

        return parent::createFormBuilder($data, $options);
    }

    private function serialize(AbstractApiResponse $apiResponse): string
    {
        if (!$apiResponse instanceof EmptyApiResponse) {
            $serializer = $this->get('jms_serializer');
            $serializedData = $serializer->serialize($apiResponse, self::FORMAT_JSON);
        }

        return $serializedData ?? '';
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
