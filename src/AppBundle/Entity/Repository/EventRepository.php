<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Infrasctucture\AbstractRepository;

/** {@inheritDoc} */
class EventRepository extends AbstractRepository
{

    /**
     * @return Event|null
     */
    public function findOneById(string $id)
    {
        return $this->find($id);
    }

    /**
     * @return Event|object|null
     */
    public function findOneByNameAndDate(string $name, \DateTime $date)
    {
        return $this->findOneBy(
            [
                'name' => $name,
                'date' => $date,
            ]
        );
    }

    /**
     * @return Event|object|null
     */
    public function findLike(string $likeString)
    {
        $queryBuilder = $this->createQueryBuilder('event');
        $queryBuilder->select('event');
        $queryBuilder->where('event.name LIKE :eventName');
        $queryBuilder->setParameter('eventName', '%' . $likeString . '%');

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
