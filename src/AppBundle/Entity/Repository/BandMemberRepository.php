<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\BandMember;
use AppBundle\Entity\Infrasctucture\AmbassadorMemberRepository;

/** {@inheritDoc} */
class BandMemberRepository extends AmbassadorMemberRepository
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
