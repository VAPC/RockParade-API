<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Band;
use Doctrine\ORM\EntityRepository;

/** {@inheritDoc} */
class BandRepository extends EntityRepository
{
    /**
     * @return Band|null
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
