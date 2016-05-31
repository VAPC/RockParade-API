<?php

namespace AppBundle\Exception;

/**
 * @author Vehsamrak
 */
class UserNotFound extends \DomainException
{

    /**
     * @param string $userLogin
     */
    public function __construct(string $userLogin = null)
    {
        parent::__construct(sprintf('User %s was not found.', $userLogin));
    }
}
