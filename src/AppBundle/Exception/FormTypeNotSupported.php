<?php

namespace AppBundle\Exception;

/**
 * @author Vehsamrak
 */
class FormTypeNotSupported extends HttpDomainException
{

    /**
     * @param string $formTypeClass
     */
    public function __construct(string $formTypeClass)
    {
        $message = sprintf('Form type "%s" not supported.', $formTypeClass);
        parent::__construct($message);
    }

}
