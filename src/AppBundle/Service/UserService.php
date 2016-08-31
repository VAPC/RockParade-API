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

    public function __construct(VkontakteClient $vkontakteClient, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->vkontakteClient = $vkontakteClient;
    }

    public function createOrUpdateUser(AccessToken $token): User
    {
        $userVkId = $token->userVkId;
        $user = $this->userRepository->findUserByVkId($userVkId);

        if ($user) {
            $user->setVkToken($token);
        } else {
            $id = IdGenerator::generateId();
            $name = $this->vkontakteClient->getUserName($token);

            $user = new User($id, $name, $userVkId, $token->getTokenHash(), $token->userEmail);

            $this->userRepository->persist($user);
        }

        $this->userRepository->flush();

        return $user;
    }
}
