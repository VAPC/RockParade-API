<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Infrasctucture\AbstractRepository;

/** {@inheritDoc} */
class EventRepository extends AbstractRepository
{

    public function findOneById(int $id)
    {
        return $this->find($id);
    }
}
