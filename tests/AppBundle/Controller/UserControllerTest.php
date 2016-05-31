<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Fixture\UserFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class UserControllerTest extends FunctionalTester
{

    const USER_NAME_NEW = 'Unit Tester';
    const USER_DESCRIPTION_NEW = 'Unit tester description';
    const USER_LOGIN_NEW = 'unittester';
    const USER_LOGIN_FIRST = 'first';
    const USER_LOGIN_SECOND = 'second';
    const USER_NAME_FIRST = 'Mr. First';
    const USER_NAME_SECOND = 'Mr. Second';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
                UserFixture::class,
            ]
        );
        parent::setUp();
    }

    /** @test */
    public function listAction_GETUsersRequest_listOfAllUsers()
    {
        $this->followRedirects();

        $this->sendGetRequest('/users');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_FIRST, $contents['data'][0]['login']);
        $this->assertEquals(self::USER_NAME_FIRST, $contents['data'][0]['name']);
        $this->assertEquals(self::USER_LOGIN_SECOND, $contents['data'][1]['login']);
        $this->assertEquals(self::USER_NAME_SECOND, $contents['data'][1]['name']);
    }

    /** @test */
    public function viewAction_GETUsersViewLoginRequest_singleUserInfo()
    {
        $this->sendGetRequest('/user/first');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_FIRST, $contents['data']['login']);
        $this->assertEquals(self::USER_NAME_FIRST, $contents['data']['name']);
    }

    /** @test */
    public function viewAction_GETUsersViewNotExistingLoginRequest_userNotFoundError()
    {
        $this->sendGetRequest('/user/notexistinguser');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(404, $responseCode);
        $this->assertContains('User with login "notexistinguser" was not found.', $contents['errors']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithLoginAndNameAndDescription_newUserCreated()
    {
        $parameters = $this->createParametersForNewUser();

        $this->sendPostRequest('/user/create', $parameters);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_NEW, $contents['data']['login']);
        $this->assertEquals(self::USER_NAME_NEW, $contents['data']['name']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithLoginOfExistingUser_userAlreadyExistsError()
    {
        $parameters = $this->createParametersWithLoginOfExistingUser();

        $this->sendPostRequest('/user/create', $parameters);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(409, $responseCode);
        $this->assertContains('User with login or username "first" already exists.', $contents['errors']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithUsernameOfExistingUser_userAlreadyExistsError()
    {
        $parameters = $this->createParametersWithUsernameOfExistingUser();

        $this->sendPostRequest('/user/create', $parameters);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(409, $responseCode);
        $this->assertContains('User with login or username "Mr. First" already exists.', $contents['errors']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithEmptyParameters_missingParametersError()
    {
        $this->sendPostRequest('/user/create', []);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(400, $responseCode);
        $this->assertContains('Properties "login" and "name" are mandatory.', $contents['errors']);
    }

    /**
     * @return array
     */
    private function createParametersForNewUser(): array
    {
        return [
            'login'       => self::USER_LOGIN_NEW,
            'name'        => self::USER_NAME_NEW,
            'description' => self::USER_DESCRIPTION_NEW,
        ];
    }

    /**
     * @return array
     */
    private function createParametersWithLoginOfExistingUser(): array
    {
        return [
            'login'       => self::USER_LOGIN_FIRST,
            'name'        => self::USER_NAME_NEW,
        ];
    }

    /**
     * @return array
     */
    private function createParametersWithUsernameOfExistingUser(): array
    {
        return [
            'login'       => self::USER_LOGIN_NEW,
            'name'        => self::USER_NAME_FIRST,
        ];
    }
}
