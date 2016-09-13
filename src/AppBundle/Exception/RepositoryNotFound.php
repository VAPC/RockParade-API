<?php

namespace AppBundle\Exception;

/**
 * @author Vehsamrak
 */
class RepositoryNotFound extends HttpDomainException
{

    /** {@inheritDoc} */
    public function __construct(string $repositoryName)
    {
        $message = sprintf('Repository "%s" was not found.', $repositoryName);

        parent::__construct($message);
    }
}
