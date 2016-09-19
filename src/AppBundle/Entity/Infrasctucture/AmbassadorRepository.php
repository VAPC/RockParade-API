<?php

namespace AppBundle\Entity\Infrasctucture;

/**
 * @author Vehsamrak
 */
class AmbassadorRepository extends AbstractRepository
{

    /**
     * @return Ambassador|object|null
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
