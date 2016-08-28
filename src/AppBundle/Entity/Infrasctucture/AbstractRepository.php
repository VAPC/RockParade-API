<?php

namespace AppBundle\Entity\Infrasctucture;

use Doctrine\ORM\EntityRepository;

/**
 * @author Vehsamrak
 */
abstract class AbstractRepository extends EntityRepository
{

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    public function flush()
    {
        $this->_em->flush();
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
        $ids = $this->getClassMetadata()->getIdentifierFieldNames();
        $id = reset($ids);
        $queryBuilder = $this->createQueryBuilder('entity');
        $queryBuilder->select($queryBuilder->expr()->count('entity.' . $id));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
