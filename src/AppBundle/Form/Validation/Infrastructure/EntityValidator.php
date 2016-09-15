<?php

namespace AppBundle\Form\Validation\Infrastructure;

use AppBundle\Exception\HttpRuntimeException;
use AppBundle\Form\Validation\EntityDoesNotExists;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Vehsamrak
 */
abstract class EntityValidator extends ConstraintValidator
{

    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityFieldValue Entity field value
     * @param EntityDoesNotExists $constraint
     */
    public function findEntity($entityFieldValue, Constraint $constraint)
    {
        try {
            $repository = $this->entityManager->getRepository($constraint->entityClass);
        } catch (ORMException $exception) {
            throw new HttpRuntimeException($exception->getMessage());
        }

        $entity = $repository->findOneBy(
            [
                $constraint->entityField => $entityFieldValue,
            ]
        );

        return $entity;
    }

    /**
     * @throws HttpRuntimeException
     */
    public function checkAnnotationParameters(EntityConstraint $constraint)
    {
        if (empty($constraint->entityClass) || empty($constraint->entityField)) {
            throw new HttpRuntimeException('"entityClass" and "entityField" annotation parameters are mandatory.');
        }

        if (!class_exists($constraint->entityClass)) {
            throw new HttpRuntimeException(
                sprintf('Class "%s" given in annotation does not exist.', $constraint->entityClass)
            );
        }
    }
}
