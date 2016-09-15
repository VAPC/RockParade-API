<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Band;

/**
 * @author Vehsamrak
 */
class BandFormType extends AmbassadorFormType
{

    public function getEntityClassName(): string
    {
        return Band::class;
    }
}
