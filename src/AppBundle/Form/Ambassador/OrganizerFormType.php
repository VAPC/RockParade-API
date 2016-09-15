<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Organizer;

/**
 * @author Vehsamrak
 */
class OrganizerFormType extends AmbassadorFormType
{
    public function getEntityClassName(): string
    {
        return Organizer::class;
    }
}
