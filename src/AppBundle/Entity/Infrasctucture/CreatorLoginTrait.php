<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 */
trait CreatorLoginTrait
{

    public function getCreatorLogin(): string
    {
        return $this->creator instanceof User
            ? $this->creator->getLogin()
            : $this->getDefaultCreatorLogin();
    }

    private function getDefaultCreatorLogin()
    {
        return 'creator unknown';
    }
}