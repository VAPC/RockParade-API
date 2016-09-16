<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Form\AbstractFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;

/**
 * @author Vehsamrak
 */
abstract class AbstractRepository extends EntityRepository
{

    /**
     * @return object|null Entity
     */
    public function findOneByFormData(AbstractFormType $formType)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    public function flush($entity = null)
    {
        $this->_em->flush($entity);
    }

    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->_em->persist($entity);
    }

    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $this->_em->remove($entity);
    }

    /**
     * @param int|string|array $id
     * @return object|null Entity
     */
    public function findOneById($id)
    {
        return $this->find($id);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return object[] Entity array
     */
    public function findAllWithLimitAndOffset(int $limit = null, int $offset = null): array
    {
        // Doctrine can handle correctly only null limits, but not 0
        if (!$limit) { $limit = null; }
        if (!$offset) { $offset = null; }

        return $this->findBy(
            [],
            null,
            $limit,
            $offset
        );
    }

    public function countAll(): int
    {
        $id = $this->getEntityIdField();
        $queryBuilder = $this->createQueryBuilder('entity');
        $queryBuilder->select($queryBuilder->expr()->count('entity.' . $id));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function getEntityIdField(): string
    {
        $ids = $this->getClassMetadata()->getIdentifierFieldNames();
        $id = reset($ids);

        return $id;
    }
}
