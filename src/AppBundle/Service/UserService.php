<?php

namespace AppBundle\Service;

use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Service\Vkontakte\AccessToken;
use AppBundle\Service\Vkontakte\VkontakteClient;

/**
 * @author Vehsamrak
 */
class UserService
{

    /** @var UserRepository */
    private $userRepository;

    /** @var VkontakteClient */
    private $vkontakteClient;

    public function __construct(
        VkontakteClient $vkontakteClient,
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->vkontakteClient = $vkontakteClient;
    }

    public function createOrUpdateUser(AccessToken $vkToken): User
    {
        $userVkId = $vkToken->userVkId;
        $user = $this->userRepository->findUserByVkId($userVkId);
        $vkTokenHash = $vkToken->getTokenHash();

        if ($user) {
            $user->updateToken();
            $user->setVkToken($vkTokenHash);
        } else {
            $id = HashGenerator::generate();
            $name = $this->vkontakteClient->getUserName($vkTokenHash);

            $user = new User(
                $id,
                $name,
                $userVkId,
                $vkTokenHash,
                $vkToken->userEmail
            );

            $this->userRepository->persist($user);
        }

        $this->userRepository->flush();

        return $user;
    }
}
