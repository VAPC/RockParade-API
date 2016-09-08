<?php

namespace AppBundle\Service\Entity;

use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Service\HashGenerator;
use AppBundle\Service\Vkontakte\AccessToken;
use AppBundle\Service\Vkontakte\VkontakteClient;

/**
 * @author Vehsamrak
 */
class UserService extends EntityService
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
        $userVkId = $vkToken->getUserVkontakteId();
        $user = $this->userRepository->findUserByVkId($userVkId);
        $vkTokenHash = $vkToken->getVkontakteTokenHash();

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

        $this->userRepository->flush($user);

        return $user;
    }
}
