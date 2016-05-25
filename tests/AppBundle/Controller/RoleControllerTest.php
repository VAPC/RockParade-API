<?php

namespace AppBundle\Controller;

use AppBundle\Fixture\RoleFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class RoleControllerTest extends FunctionalTester
{

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
}
