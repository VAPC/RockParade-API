<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Vehsamrak
 */
class EventFixture implements FixtureInterface
{

    /** {@inheritDoc} */
    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository(User::class)->findOneByLogin('first');

        $entities = [
            new Event('Test Event', $user, new \DateTime('2187-03-03 10:10'), 'Great event, please come!'),
            new Event('Second Test Event', $user, new \DateTime('1969-03-03 10:10'), 'Woodstocky old event!'),
        ];

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
