<?php

namespace AppBundle\Controller;

use AppBundle\Fixture\RoleFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class RoleControllerTest extends FunctionalTester
{

    const USER_LOGIN_FIRST = 'first';
    const ROLE_ADMIN = 'admin';
    const ROLE_NONEXISTENT = 'nonexistent';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
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
        $this->assertEquals('admin', $contents['data'][0]['name']);
        $this->assertEquals('Администратор', $contents['data'][0]['description']);
    }

    /** @test */
    public function addAction_POSTRolesAddRequestWithLoginAndRoles_response200()
    {
        $parameters = $this->createParametersLoginAndRoles();

        $this->sendPostRequest('/roles/add', $parameters);
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
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
    private function createParametersLoginAndRoles(): array
    {
        return [
            'login' => self::USER_LOGIN_FIRST,
            'roles' => [self::ROLE_ADMIN],
        ];
    }

    /**
     * @return array
     */
    private function createParametersLoginAndNonexistentRoles(): array
    {
        $parameters = $this->createParametersLoginAndRoles();
        $parameters['roles'][] = self::ROLE_NONEXISTENT;

        return $parameters;
    }
}
