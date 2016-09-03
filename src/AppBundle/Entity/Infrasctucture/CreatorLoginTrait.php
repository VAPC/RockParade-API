<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
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