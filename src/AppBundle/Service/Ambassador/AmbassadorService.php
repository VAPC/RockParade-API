<?php

namespace AppBundle\Service\Ambassador;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Infrasctucture\Ambassador;
use AppBundle\Entity\Infrasctucture\AmbassadorMemberRepository;
use AppBundle\Entity\Infrasctucture\AmbassadorRepository;
use AppBundle\Entity\User;
use AppBundle\Exception\FormTypeNotSupported;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\Ambassador\AmbassadorFormType;
use AppBundle\Form\Ambassador\AmbassadorMemberFormType;
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

    public function createEntityByFormData(AbstractFormType $formType, User $creator)
    {
        if ($formType instanceof AmbassadorFormType) {
            $entity = $this->createAmbassador($formType, $creator);
        } elseif ($formType instanceof AmbassadorMemberFormType) {
            $entity = $this->createAmbassadorMember($formType);
        } else {
            throw new FormTypeNotSupported(get_class($formType));
        }

        $this->entityManager->flush($entity);

        return $entity;
    }

    private function getOrCreateMemberByAmbassadorAndUserLogin(
        Ambassador $ambassador,
        $memberLogin,
        $memberShortDescription = '',
        $memberDescription = ''
    ) {
        $user = $this->entityManager->getRepository(User::class)->findOneByLogin($memberLogin);
        /** @var AmbassadorMemberRepository $repository */
        $repository = $this->entityManager->getRepository($ambassador->getMemberClass());

        return $repository->getOrCreateByAmbassadorAndUser(
            $ambassador,
            $user,
            $memberShortDescription,
            $memberDescription
        );

    }

    private function createAmbassador(AmbassadorFormType $formType, User $creator)
    {
        $ambassadorName = $formType->name;
        $ambassadorDescription = $formType->description;
        $ambassadorMemberLogins = (array) $formType->members;

        /** @var Ambassador $ambassador */
        $entityClass = $formType->getEntityClassName();
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
                $member = $this->getOrCreateMemberByAmbassadorAndUserLogin(
                    $ambassador,
                    $memberLogin,
                    $memberShortDescription,
                    $memberDescription
                );

                $ambassador->addMember($member);
            }
        }

        return $ambassador;
    }

    private function createAmbassadorMember(AmbassadorMemberFormType $formType)
    {
        $ambassadorId = $formType->ambassador;
        /** @var AmbassadorRepository $ambassadorRepository */
        $ambassadorRepository = $this->entityManager->getRepository($formType->getEntityClassName());
        $ambassador = $ambassadorRepository->findOneById($ambassadorId);

        return $this->getOrCreateMemberByAmbassadorAndUserLogin(
            $ambassador,
            $formType->login,
            $formType->shortDescription,
            $formType->description
        );
    }
}
