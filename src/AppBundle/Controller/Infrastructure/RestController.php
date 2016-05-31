<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Response\ApiResponse;
use AppBundle\Response\EmptyApiResponse;
use AppBundle\Response\HttpCodeInterface;
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

    /**
     * @param ApiResponse|EmptyApiResponse|HttpCodeInterface $apiResponse
     * @return Response
     */
    public function respond(HttpCodeInterface $apiResponse): Response
    {
        $serializedData = '';

        if (!$apiResponse instanceof EmptyApiResponse) {
            $serializer = $this->get('jms_serializer');
            $serializedData = $serializer->serialize($apiResponse, self::FORMAT_JSON);
        }

        $response = new Response($serializedData, $apiResponse->getHttpCode());
        $response->headers->set('Content-Type', 'application/json');

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
}
