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

    const TEST_TOKEN = 'test-token';
    const TEST_TOKEN_SECOND = 'test-token-second';
    const TEST_TOKEN_THIRD = 'test-token-third';

    /** {@inheritDoc} */
    public function load(ObjectManager $manager)
    {
        $users = [
            new User('first', 'Mr. First', 4, '', null, 'The very first test user.', self::TEST_TOKEN),
            new User('second', 'Mr. Second', 5, '', null, 'The second test user.', self::TEST_TOKEN_SECOND),
            new User('third', 'Mr. Third', 6, '', null, 'The third test user.', self::TEST_TOKEN_THIRD),
            new User('bander', 'Bander', 7, ''),
        ];

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }
}
