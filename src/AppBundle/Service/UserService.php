<?php

namespace AppBundle\Service;

use AppBundle\Entity\Repository\UserRepository;

/**
 * @author Vehsamrak
 */
class UserService
{

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
