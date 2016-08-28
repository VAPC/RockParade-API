<?php

namespace AppBundle\Service;

use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Repository\BandMemberRepository;
use AppBundle\Entity\Repository\BandRepository;
use AppBundle\Entity\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * @author Vehsamrak
 */
class BandService
{
    const ATTRIBUTE_MEMBERS = 'members';

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

    public function processFormAndCreateBand(FormInterface $form): FormInterface
    {
        return $this->createOrUpdateBand($form);
    }

    public function processFormAndUpdateBand(FormInterface $form, Band $band): FormInterface
    {
        return $this->createOrUpdateBand($form, $band);
    }

    /**
     * Create or update band. If no Band object passed, new one will be created
     * @param Band|null $band
     */
    private function createOrUpdateBand(FormInterface $form, Band $band = null): FormInterface
    {
        $this->createOrUpdateBandUsingForm($form, $band);
        $this->bandRepository->flush();

        return $form;
    }

    private function createOrUpdateBandUsingForm(
        FormInterface $form,
        Band $band = null
    ) {
        if (!$form->isValid()) {
            return null;
        }

        $membersData = $form->get(self::ATTRIBUTE_MEMBERS)->getData();

        if (!$membersData) {
            $form->addError(new FormError(sprintf('Parameter "%s" is mandatory.', self::ATTRIBUTE_MEMBERS)));

            return null;
        }

        $bandNewName = $form->get('name')->getData();
        $description = $form->get('description')->getData();

        if ($this->bandRepository->findOneByName($bandNewName)) {
            $form->addError(new FormError(sprintf('Band with name "%s" already exists.', $bandNewName)));

            return null;
        }

        if ($band) {
            $band->setName($bandNewName);
            $band->setDescription($description);
        } else {
            $band = new Band($bandNewName, $description);
            $this->bandRepository->persist($band);
        }

        $bandMembers = $this->getBandMembersFromForm($band, $form);

        foreach ($bandMembers as $bandMember) {
            $band->addMember($bandMember);
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

                return $this->bandMemberRepository->getOrCreateByBandAndUser($band, $user, $shortDescription);
            },
            $form->get(self::ATTRIBUTE_MEMBERS)->getData()
        );
    }

    /**
     * @param FormInterface $form
     * @return string[]
     */
    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
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
}
