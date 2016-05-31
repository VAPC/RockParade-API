<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Role;
use AppBundle\Entity\Repository\RoleRepository;
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

        /** @var Role $role */
        foreach ($roles as $role) {
            /** @var RoleRepository $roleRepository */
            $roleRepository = $manager->getRepository(Role::class);

            if (empty($roleRepository->findOneByName($role->getName()))) {
                $manager->persist($role);
            }
        }

        $manager->flush();
    }
}
