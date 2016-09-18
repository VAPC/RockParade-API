<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Exception\HttpRuntimeException;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\Ambassador\AmbassadorFormType;

/**
 * @author Vehsamrak
 */
class AmbassadorRepository extends AbstractRepository
{

    /**
     * @return Ambassador|object|null
     */
    public function findOneByName(string $name)
    {
        return $this->findOneBy(
            [
                'name' => $name,
            ]
        );
    }
}
