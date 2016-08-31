<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Vehsamrak
 */
class UserFixture implements FixtureInterface
{

    /** {@inheritDoc} */
    public function load(ObjectManager $manager)
    {
        $users = [
            new User('first', 'Mr. First', 1, '', null, 'The very first test user.'),
            new User('second', 'Mr. Second', 1, '', null, 'The second test user.'),
        ];

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }
}
