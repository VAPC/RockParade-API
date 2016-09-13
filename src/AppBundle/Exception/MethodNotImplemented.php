<?php

namespace AppBundle\Exception;

/**
 * @author Vehsamrak
 */
class MethodNotImplemented extends HttpDomainException
{

    /**
     * @param string $methodName
     */
    public function __construct(string $methodName)
    {
        $message = sprintf('Method %s() not implemented.', $methodName);

        parent::__construct($message);
    }
}
