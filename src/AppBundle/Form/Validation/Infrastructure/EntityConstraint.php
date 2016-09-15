<?php

namespace AppBundle\Form\Validation\Infrastructure;

use Symfony\Component\Validator\Constraint;

/**
 * @author Vehsamrak
 */
class EntityConstraint extends Constraint
{
    public $entityClass = '';
    public $entityField = '';
}
