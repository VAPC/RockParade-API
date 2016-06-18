<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Infrasctucture\AbstractRepository;
use AppBundle\Entity\User;

/** {@inheritDoc} */
class BandMemberRepository extends AbstractRepository
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

    public function getOrCreateByBandAndUser(Band $band, User $newUser, string $shortDescription = '', string $description = ''): BandMember
    {
        $bandMember = $this->findOneBy(
            [
                'band' => $band,
                'user' => $newUser,
            ]
        );
        
        if (!$bandMember) {
            $bandMember = new BandMember($band, $newUser, $shortDescription, $description);
            $this->persist($bandMember);
        }
        
        return $bandMember;
    }
}
