<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 * @property $creator
 */
trait CreatorLoginTrait
{

    abstract function getCreator(): User;

    public function getCreatorLogin(): string
    {
        return $this->getCreator()->getLogin();
    }
}