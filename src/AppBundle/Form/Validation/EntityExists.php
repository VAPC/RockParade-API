<?php

namespace AppBundle\Form\Validation;

use AppBundle\Form\Validation\Infrastructure\EntityConstraint;

/**
 * Validate that entity with given field value exists in database
 * @author Vehsamrak
 * @Annotation
 */
class EntityExists extends EntityConstraint
{

    public $message = '%entityName% with %field% "%fieldValue%" does not exists.';
}
