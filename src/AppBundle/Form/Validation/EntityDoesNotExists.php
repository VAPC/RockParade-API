<?php

namespace AppBundle\Form\Validation;

use AppBundle\Form\Validation\Infrastructure\EntityConstraint;

/**
 * Validate that entity with given field value does not exists in database
 * @author Vehsamrak
 * @Annotation
 */
class EntityDoesNotExists extends EntityConstraint
{

    public $message = '%entityName% with %field% "%fieldValue%" already exists.';
}
