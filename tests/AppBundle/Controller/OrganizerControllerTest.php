<?php

namespace Tests\AppBundle\Controller;

use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class OrganizerControllerTest extends FunctionalTester
{

    /** @test */
    public function listAction_GETOgranizersRequest_listAllOrganizers()
    {
        $this->followRedirects();

        $this->sendGetRequest('/organizers');
        $listBandsResponseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertTrue(array_key_exists('total', $contents));
        $this->assertTrue(array_key_exists('limit', $contents));
        $this->assertTrue(array_key_exists('offset', $contents));
    }
}
