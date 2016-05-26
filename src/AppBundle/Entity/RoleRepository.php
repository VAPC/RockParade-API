<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Infrasctucture\AbstractRepository;

/** {@inheritDoc} */
class RoleRepository extends AbstractRepository
{

    /**
     * @param string $name
     * @return null|Role
     */
    public function findOneByName(string $name)
    {
        return $this->findOneBy(
            [
                'name' => $name,
            ]
        );
    }

    /**
     * @param string[] $roleNames
     * @return int
     */
    public function countRolesWithNames(array $roleNames): int
    {
        $queryBuilder = $this->createQueryBuilder('role');
        $queryBuilder->select('count(role.name)');
        $queryBuilder->where($queryBuilder->expr()->in('role.name', ':roleNames'));
        $queryBuilder->setParameter('roleNames', $roleNames);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
