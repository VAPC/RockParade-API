<?php

namespace AppBundle\Controller;

use AppBundle\Fixture\RoleFixture;
use AppBundle\Fixture\UserFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class RoleControllerTest extends FunctionalTester
{

    const USER_LOGIN_FIRST = 'first';
    const ROLE_ADMIN = 'admin';
    const ROLE_NONEXISTENT = 'nonexistent';
    const ROLE_MUSICIAN = 'musician';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
                UserFixture::class,
                RoleFixture::class,
            ]
        );
        parent::setUp();
    }

    /** @test */
    public function listAction_GETRolesRequest_listOfAllRoles()
    {
        $this->followRedirects();
        
        $this->sendGetRequest('/roles');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals('Администратор', $contents['data']['admin']['description']);
    }

    /** @test */
    public function addAction_POSTRolesAddRequestWithLoginAndRoles_adminAndMusicianRolesAppliedToUser()
    {
        $this->followRedirects();
        $parameters = $this->createParametersLoginFirstAndRolesAdminMusician();

        $this->sendPostRequest('/roles/add', $parameters);
        $responseCode = $this->getResponseCode();
        $this->assertEquals(200, $responseCode);

        $this->sendGetRequest('/roles');
        $contents = $this->getResponseContents();
        $this->assertEquals(200, $responseCode);
        $this->assertTrue(in_array(self::USER_LOGIN_FIRST, $contents['data']['admin']['users']));
        $this->assertTrue(in_array(self::USER_LOGIN_FIRST, $contents['data']['musician']['users']));
    }

    /** @test */
    public function addAction_POSTRolesAddRequestWithEmptyParameters_response400AndErrorMessage()
    {
        $this->sendPostRequest('/roles/add', []);
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(400, $responseCode);
        $this->assertEquals('Properties "login" and "roles" are mandatory.', $contents['error']);
    }

    /** @test */
    public function addAction_POSTRolesAddRequestWithLoginAndNonexistentRole_response400AndErrorMessage()
    {
        $parameters = $this->createParametersLoginAndNonexistentRoles();

        $this->sendPostRequest('/roles/add', $parameters);
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(400, $responseCode);
        $this->assertEquals('Not all provided roles are valid.', $contents['error']);
    }

    /**
     * @return array
     */
    private function createParametersLoginFirstAndRolesAdminMusician(): array
    {
        return [
            'login' => self::USER_LOGIN_FIRST,
            'roles' => [self::ROLE_ADMIN, self::ROLE_MUSICIAN],
        ];
    }

    /**
     * @return array
     */
    private function createParametersLoginAndNonexistentRoles(): array
    {
        $parameters = $this->createParametersLoginFirstAndRolesAdminMusician();
        $parameters['roles'][] = self::ROLE_NONEXISTENT;

        return $parameters;
    }
}
