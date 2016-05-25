<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * User roles fixture
 * @author Vehsamrak
 */
class RoleFixture implements FixtureInterface
{

    /** {@inheritDoc} */
    public function load(ObjectManager $manager)
    {
        $roles = [
            new Role('admin', 'Администратор'),
            new Role('moderator', 'Модератор'),
            new Role('musician', 'Музыкант'),
            new Role('organizer', 'Организатор'),
        ];

        foreach ($roles as $role) {
            $manager->persist($role);
        }

        $manager->flush();
    }
}
