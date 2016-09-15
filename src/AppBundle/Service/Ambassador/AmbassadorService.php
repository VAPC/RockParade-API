<?php

namespace AppBundle\Service\Ambassador;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Infrasctucture\Ambassador;
use AppBundle\Entity\Infrasctucture\AmbassadorRepository;
use AppBundle\Entity\User;
use AppBundle\Exception\FormTypeNotSupported;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\Ambassador\AmbassadorFormType;
use AppBundle\Service\Entity\BandService;
use AppBundle\Service\Entity\Infrastructure\EntityCreatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * @author Vehsamrak
 */
class AmbassadorService implements EntityCreatorInterface
{

    /** @var EntityManager */
    private $entityManager;

    /** @var BandService */
    private $bandService;

    public function __construct(EntityManager $entityManager, BandService $bandService)
    {
        $this->entityManager = $entityManager;
        $this->bandService = $bandService;
    }

    public function createEntityByFormData(AbstractFormType $formType, User $creator, string $entityClass)
    {
        if (!$formType instanceof AmbassadorFormType) {
            throw new FormTypeNotSupported(get_class($formType));
        }

        $ambassadorName = $formType->name;
        $ambassadorDescription = $formType->description;
        $ambassadorMemberLogins = (array) $formType->members;

        /** @var Ambassador $ambassador */
        $ambassador = new $entityClass($ambassadorName, $creator, $ambassadorDescription);
        /** @var AmbassadorRepository $repository */
        $repository = $this->entityManager->getRepository($entityClass);
        $repository->persist($ambassador);


        if ($ambassador instanceof Band) {
            $ambassador->addMember($this->bandService->createBandMemberFromCreator($ambassador, $creator));

            foreach ($ambassadorMemberLogins as $memberData) {
                $memberLogin = $memberData['login'];
                $memberShortDescription = $memberData['short_description'];
                $memberDescription = $memberData['description'] ?? '';

                /** @var BandMember $member */
                $member = $this->getMemberByAmbassadorAndLogin(
                    $ambassador,
                    $memberLogin,
                    $memberShortDescription,
                    $memberDescription
                );

                $ambassador->addMember($member);
            }
        }

        $repository->flush();

        return $ambassador;
    }

    private function getMemberByAmbassadorAndLogin(
        $ambassador,
        $memberLogin,
        $memberShortDescription = '',
        $memberDescription = ''
    ) {
        $user = $this->entityManager->getRepository(User::class)->findOneByLogin($memberLogin);

        return $this->entityManager->getRepository(BandMember::class)->getOrCreateByBandAndUser(
            $ambassador,
            $user,
            $memberShortDescription,
            $memberDescription
        );

    }
}
