<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 */
trait GetUserLoginsTrait
{
    /**
     * @return string[]
     */
    public function getUserLogins(): array
    {
        return array_map(function (User $user) {
            return $user->getLogin();
        }, $this->getUsers()->toArray());
    }
}