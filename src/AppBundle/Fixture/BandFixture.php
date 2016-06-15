<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Vehsamrak
 */
class BandFixture implements FixtureInterface
{

    /** {@inheritDoc} */
    public function load(ObjectManager $manager)
    {
        $user = new User('bander', 'Bander');
        $band = new Band('Banders', [$user], 'Band description.');
        $bandMember = new BandMember($user, $band, 'bass guitar', 'Hard rocker was the second musician in this band.');

        $entities = [
            $user,
            new User('derban', 'Derban'),
            new User('rocker', 'Hard Rocker'),
            $band,
            new Band('Existing Band', [$user], 'Second Band description.'),
            $bandMember,
        ];

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
