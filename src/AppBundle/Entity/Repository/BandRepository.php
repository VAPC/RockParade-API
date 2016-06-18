<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Band;
use AppBundle\Entity\Infrasctucture\AbstractRepository;
use Doctrine\ORM\EntityRepository;

/** {@inheritDoc} */
class BandRepository extends AbstractRepository
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
