<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Organizer;
use AppBundle\Entity\OrganizerMember;
use AppBundle\Entity\User;
use AppBundle\Service\ForegoneHashGenerator;
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
        $creator = $manager->getRepository(User::class)->findOneByLogin('first');
        $organizer = new Organizer('Org', $creator, 'Organizer description.', new ForegoneHashGenerator('test-organizer'));
        $organizerMember = new OrganizerMember($organizer, $creator);

        $entities = [
            new User('derban', 'Derban', 2, ''),
            new User('rocker', 'Hard Rocker', 3, ''),
            $organizer,
            $organizerMember,
            new Organizer('Existing Organizer', $creator, 'Second Organizer description.'),
        ];

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
