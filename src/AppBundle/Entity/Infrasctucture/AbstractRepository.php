<?php

namespace AppBundle\Entity\Infrasctucture;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;

/**
 * @author Vehsamrak
 */
class AbstractRepository extends EntityRepository
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
     * @param $entity
     */
    public function persist($entity)
    {
        $this->_em->persist($entity);
    }
}
