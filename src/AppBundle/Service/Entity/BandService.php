<?php

namespace AppBundle\Service\Entity;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Infrasctucture\Ambassador;
use AppBundle\Entity\Infrasctucture\AmbassadorMember;
use AppBundle\Entity\Repository\BandMemberRepository;
use AppBundle\Entity\Repository\BandRepository;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * @author Vehsamrak
 */
class BandService extends EntityService
{
    const ATTRIBUTE_MEMBERS = 'members';
    const CREATOR_DEFAULT_MEMBER_SHORT_DESCRIPTION = 'Founder';

    /** @var BandRepository */
    private $bandRepository;

    /** @var BandMemberRepository */
    private $bandMemberRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        BandRepository $bandRepository,
        BandMemberRepository $bandMemberRepository,
        UserRepository $userRepository
    ) {
        $this->bandRepository = $bandRepository;
        $this->bandMemberRepository = $bandMemberRepository;
        $this->userRepository = $userRepository;
    }

    public function processFormAndUpdateBand(FormInterface $form, Band $band, User $creator): FormInterface
    {
        return $this->createOrUpdateBand($form, $creator, $band);
    }

    /**
     * Create or update band. If no Band object passed, new one will be created
     */
    private function createOrUpdateBand(FormInterface $form, User $creator, Band $band = null): FormInterface
    {
        $this->createOrUpdateBandUsingForm($form, $creator, $band);
        $this->bandRepository->flush();

        return $form;
    }

    private function createOrUpdateBandUsingForm(
        FormInterface $form,
        User $creator,
        Band $band = null
    ) {
        if (!$form->isValid()) {
            return null;
        }

        $bandNewName = $form->get('name')->getData();
        $description = $form->get('description')->getData();

        if ($band && $band->getName() !== $bandNewName && $this->bandRepository->findOneByName($bandNewName)) {
            $form->addError(new FormError(sprintf('Band with name "%s" already exists.', $bandNewName)));

            return null;
        }

        if ($band) {
            $band->setName($bandNewName);
            $band->setDescription($description);
        } else {
            $band = new Band($bandNewName, $creator, $description);
            $this->bandRepository->persist($band);
        }

        if ($form->get(self::ATTRIBUTE_MEMBERS)->getData()) {
            $bandMembers = $this->getBandMembersFromForm($band, $form);
            $bandMembers[] = $this->createAmbassadorMemberFromCreator($band, $creator);

            foreach ($bandMembers as $bandMember) {
                $band->addMember($bandMember);
            }
        }
    }

    /**
     * @return BandMember[]
     */
    private function getBandMembersFromForm(Band $band, FormInterface $form): array
    {
        return array_map(
            function (array $userData) use ($band, $form) {
                $user = null;
                $shortDescription = '';

                if (isset($userData['login'], $userData['short_description'])) {
                    $userLogin = $userData['login'];
                    $shortDescription = $userData['short_description'];
                    $user = $this->userRepository->findOneByLogin($userLogin);

                    if (!$user) {
                        $form->addError(new FormError(sprintf('User "%s" was not found.', $userLogin)));
                    }
                } else {
                    $form->addError(
                        new FormError('Group member parameters login and short_description are mandatory.')
                    );
                }

                return $this->bandMemberRepository->getOrCreateByAmbassadorAndUser($band, $user, $shortDescription);
            },
            $form->get(self::ATTRIBUTE_MEMBERS)->getData()
        );
    }

    public function processFormAndUpdateBandMember(FormInterface $form, BandMember $bandMember): FormInterface
    {
        $shortDescription = $form->get('short_description')->getData();
        $description = $form->get('description')->getData();

        if ($shortDescription) {
            $bandMember->setShortDescription($shortDescription);
        }

        if ($description) {
            $bandMember->setDescription($description);
        }

        return $form;
    }

    public function createAmbassadorMemberFromCreator(Ambassador $ambassador, User $creator): AmbassadorMember
    {
        return $this->bandMemberRepository->getOrCreateByAmbassadorAndUser(
            $ambassador,
            $creator,
            self::CREATOR_DEFAULT_MEMBER_SHORT_DESCRIPTION
        );
    }
}
