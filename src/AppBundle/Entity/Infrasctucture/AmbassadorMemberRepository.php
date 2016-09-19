<?php

namespace AppBundle\Entity\Infrasctucture;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\User;

/**
 * @author Vehsamrak
 */
class AmbassadorMemberRepository extends AbstractRepository
{

    /**
     * @param Ambassador $ambassador
     * @param User $user
     * @return AmbassadorMember|object|null
     */
    public function findByAmbassadorAndUser(Ambassador $ambassador, User $user)
    {
        return $this->findOneBy(
            [
                'ambassador' => $ambassador,
                'user'       => $user,
            ]
        );
    }

    public function getOrCreateByAmbassadorAndUser(
        Ambassador $ambassador,
        User $user,
        string $shortDescription = '',
        string $description = null
    ): AmbassadorMember
    {
        $ambassadorMember = $this->findByAmbassadorAndUser($ambassador, $user);

        if (!$ambassadorMember) {
            $ambassadorClass = $ambassador->getMemberClass();
            $ambassadorMember = new $ambassadorClass($ambassador, $user, $shortDescription, $description);
            $this->persist($ambassadorMember);
        }

        return $ambassadorMember;
    }
}
