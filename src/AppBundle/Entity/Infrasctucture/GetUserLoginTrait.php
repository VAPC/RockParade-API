<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Vehsamrak
 * @property ArrayCollection $users
 * @property User $user
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