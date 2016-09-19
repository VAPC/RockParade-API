<?php

namespace AppBundle\Service\Entity\Infrastructure;

use AppBundle\Entity\User;
use AppBundle\Form\AbstractFormType;

/**
 * @author Vehsamrak
 */
interface EntityCreatorInterface
{

    public function createEntityByFormData(AbstractFormType $formType, User $creator);
}
