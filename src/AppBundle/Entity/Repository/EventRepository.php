<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Event;
use AppBundle\Entity\Infrasctucture\AbstractRepository;
use Doctrine\Common\Collections\ArrayCollection;

/** {@inheritDoc} */
class EventRepository extends AbstractRepository
{

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
     * @return Event[]
     */
    public function findLike(string $likeString): ArrayCollection
    {
        $queryBuilder = $this->createQueryBuilder('event');
        $queryBuilder->select('event');
        $queryBuilder->where('event.name LIKE :eventName');
        $queryBuilder->setParameter('eventName', '%' . $likeString . '%');

        return new ArrayCollection($queryBuilder->getQuery()->getResult());
    }
}
