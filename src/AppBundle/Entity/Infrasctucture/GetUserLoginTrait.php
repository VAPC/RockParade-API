<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 */
trait GetUserLoginTrait
{
    /**
     * @return string[]
     */
    public function getUserLogins(): array
    {
        return array_map(function (User $user) {
            return $user->getLogin();
        },
            $this->users->toArray()
        );
    }
    
    public function getUserLogin(): string
    {
        return $this->user->getLogin();
    }
}