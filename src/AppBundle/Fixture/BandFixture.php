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
        $user = new User('bander', 'Bander', 1, '');
        $band = new Band('Banders', 'Band description.');
        $bandMember = new BandMember($band, $user, 'bass guitar', 'loremus unitus');

        $entities = [
            $user,
            new User('derban', 'Derban', 2, ''),
            new User('rocker', 'Hard Rocker', 3, ''),
            $band,
            new Band('Existing Band', 'Second Band description.'),
            $bandMember,
        ];

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
