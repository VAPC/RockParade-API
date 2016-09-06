<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 * @property $creator
 */
trait CreatorLoginTrait
{

    /**
     * @return null|string
     */
    public function getCreatorLogin()
    {
        return $this->creator instanceof User
            ? $this->creator->getLogin()
            : null;
    }
}