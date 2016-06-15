<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\BandMember;
use Doctrine\ORM\EntityRepository;

/** {@inheritDoc} */
class BandMemberRepository extends EntityRepository
{

    /**
     * @return BandMember[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByBandName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('band_member');
        $queryBuilder->where('band_member.band = :bandName');
        $queryBuilder->setParameter('bandName', $name);
        
        return $queryBuilder->getQuery()->getResult();
    }
}
