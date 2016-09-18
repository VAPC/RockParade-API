<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Organizer;

/**
 * @author Vehsamrak
 */
class OrganizerMemberFormType extends AmbassadorMemberFormType
{

    public function getEntityClassName(): string
    {
        return Organizer::class;
    }
}
