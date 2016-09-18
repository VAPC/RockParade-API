<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Band;

/**
 * @author Vehsamrak
 */
class BandMemberFormType extends AmbassadorMemberFormType
{

    public function getEntityClassName(): string
    {
        return Band::class;
    }
}
