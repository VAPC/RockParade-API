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
     * @return Role[]
     */
    public function findByNames(array $roleNames): array
    {
        return $this->findBy(
            [
                'name' => $roleNames,
            ]
        );
    }
}
