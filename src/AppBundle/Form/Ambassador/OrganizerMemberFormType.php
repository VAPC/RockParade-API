<?php

namespace AppBundle\Form\Ambassador;

use AppBundle\Entity\Organizer;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Validation as EntityAssert;

/**
 * @author Vehsamrak
 */
class OrganizerMemberFormType extends AmbassadorMemberFormType
{

    /**
     * @var string
     * @EntityAssert\EntityExists(entityClass="AppBundle\Entity\Organizer", entityField="id")
     */
    public $ambassador;

    public function getEntityClassName(): string
    {
        return Organizer::class;
    }
}
