<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Service\UserService;
use AppBundle\Service\Vkontakte\AccessToken;
use AppBundle\Service\Vkontakte\VkontakteClient;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @author Vehsamrak
 */
class UserServiceTest extends WebTestCase
{

    const VKONTAKTE_TOKEN = 'vk-token';
    const VKONTAKTE_USER_NAME = 'tester';
    const EXISTING_USER_VKONTAKTE_ID = 1;
    const UNEXISTING_USER_VKONTAKTE_ID = 2;

    /** @test */
    public function createOrUpdateUser_accessTokenWithVkIdAndWithVkTokenHashAndUserWithSameVkIdExistsInDatabase_existingUserReturnedAndVkTokenSetAndApplicationTokenUpdatedToHim()
    {
        $accessToken = new AccessToken(self::EXISTING_USER_VKONTAKTE_ID, self::VKONTAKTE_TOKEN);
        $userService = $this->createUserService();

        $user = $userService->createOrUpdateUser($accessToken);
        \Phake::verify($user)->updateToken();
        \Phake::verify($user)->setVkToken(self::VKONTAKTE_TOKEN);

        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function createOrUpdateUser_accessTokenWithVkIdAndWithVkTokenHashAndUserWithSameVkIdDoesNotExistsInDatabase_newUserCreatedAndReturnedAndVkTokenAndApplicationTokenUpdatedToHim()
    {
        $accessToken = new AccessToken(self::UNEXISTING_USER_VKONTAKTE_ID, self::VKONTAKTE_TOKEN);
        $userService = $this->createUserService();

        $user = $userService->createOrUpdateUser($accessToken);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(self::VKONTAKTE_USER_NAME, $user->getName());
        $this->assertEquals(8, strlen($user->getUsername()));
        $this->assertEquals(32, strlen($user->getToken()));
    }

    private function createUserService(): UserService
    {
        $vkontakteClient = \Phake::mock(VkontakteClient::class);
        \Phake::when($vkontakteClient)->getUserName(\Phake::anyParameters())->thenReturn(self::VKONTAKTE_USER_NAME);

        $user = \Phake::mock(User::class);

        $userRepository = \Phake::mock(UserRepository::class);
        \Phake::when($userRepository)->findUserByVkId(self::EXISTING_USER_VKONTAKTE_ID)->thenReturn($user);

        return new UserService($vkontakteClient, $userRepository);
    }
}
