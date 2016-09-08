<?php

namespace AppBundle\Response;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class ApiValidationError extends ApiError
{

    /** {@inheritDoc} */
    public function __construct(FormInterface $form)
    {
        /** @var string[] $errors */
        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            $parametersString = join(',', $error->getMessageParameters());
            if (!$parametersString || in_array($parametersString, ['null', 'array'])) {
                $errors[] = $error->getMessage();
            } else {
                $errors[] = sprintf('%s - %s', $parametersString, $error->getMessage());
            }
        }

        parent::__construct($errors, Response::HTTP_BAD_REQUEST);
    }
}
