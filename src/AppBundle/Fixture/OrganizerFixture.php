<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Organizer;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Vehsamrak
 */
class OrganizerFixture implements FixtureInterface
{

    /** {@inheritDoc} */
    public function load(ObjectManager $manager)
    {
        $user = new User('bander', 'Bander', 1, '');
        $organizer = new Organizer('Org', $user, 'Organizer description.');

        $entities = [
            $user,
            new User('derban', 'Derban', 2, ''),
            new User('rocker', 'Hard Rocker', 3, ''),
            $organizer,
            new Organizer('Existing Organizer', $user, 'Second Organizer description.'),
        ];

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
