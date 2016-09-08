<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Fixture\UserFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class UserControllerTest extends FunctionalTester
{

    const USER_LOGIN_FIRST = 'first';
    const USER_NAME_FIRST = 'Mr. First';

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
    public function viewAction_GETUserViewLoginRequest_singleUserInfo()
    {
        $this->sendGetRequest('/user/first');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_FIRST, $contents['data']['login']);
        $this->assertEquals(self::USER_NAME_FIRST, $contents['data']['name']);
        $this->assertEquals(16, strlen($contents['data']['registration_date']));
    }

    /** @test */
    public function viewAction_GETUserViewNotExistingLoginRequest_userNotFoundError()
    {
        $this->sendGetRequest('/user/notexistinguser');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(404, $responseCode);
        $this->assertContains('User "notexistinguser" was not found.', $contents['errors']);
    }

    /** @test */
    public function viewCurrentAction_GETUserRequest_currentUserInfo()
    {
        $this->sendGetRequest('/user');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_FIRST, $contents['data']['login']);
        $this->assertEquals(self::USER_NAME_FIRST, $contents['data']['name']);
    }
}
