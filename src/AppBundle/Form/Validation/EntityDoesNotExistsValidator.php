<?php

namespace AppBundle\Form\Validation;

use AppBundle\Form\Validation\Infrastructure\EntityValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author Vehsamrak
 */
class EntityDoesNotExistsValidator extends EntityValidator
{

    /**
     * @param mixed $entityFieldValue
     * @param EntityDoesNotExists $constraint
     */
    public function validate($entityFieldValue, Constraint $constraint)
    {
        $this->checkAnnotationParameters($constraint);

        $entity = $this->findEntity($entityFieldValue, $constraint);
        $entityName = (new \ReflectionClass($constraint->entityClass))->getShortName();

        if ($entity) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('%entityName%', $entityName)
                          ->setParameter('%field%', $constraint->entityField)
                          ->setParameter('%fieldValue%', $entityFieldValue)
                          ->addViolation();
        }
    }
}
