<?php

namespace AppBundle\Fixture;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\User;
use AppBundle\Service\ForegoneHashGenerator;
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
        $creator = $manager->getRepository(User::class)->findOneByLogin('first');
        $band = new Band('Banders', $creator, 'Band description.', new ForegoneHashGenerator('Banders'));
        $bandMember = new BandMember($band, $creator, 'bass guitar', 'loremus unitus');

        $entities = [
            new User('derban', 'Derban', 2, ''),
            new User('rocker', 'Hard Rocker', 3, ''),
            $band,
            new Band('Existing Band', $creator, 'Second Band description.'),
            $bandMember,
        ];

        foreach ($entities as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();
    }
}
