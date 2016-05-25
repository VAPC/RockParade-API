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
}
