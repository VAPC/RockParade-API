<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Fixture\OrganizerFixture;
use AppBundle\Fixture\UserFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class OrganizerControllerTest extends FunctionalTester
{

    const ORGANIZER_NAME_FIRST = 'Org';
    const ORGANIZER_DESCRIPTION_FIRST = 'Organizer description.';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
                UserFixture::class,
                OrganizerFixture::class,
            ]
        );
        parent::setUp();
    }

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

    /** @test */
    public function viewAction_GETOrganizerUnexistingIdRequest_organizerResourceReturned()
    {
        $this->sendGetRequest('/organizer/unexisting');
        $contents = $this->getResponseContents();

        $this->assertEquals(404, $this->getResponseCode());
        $this->assertContains('Organizer "unexisting" was not found.', $contents['errors']);
    }

    /** @test */
    public function viewAction_GETOrganizerExistingIdRequest_organizerResourceReturned()
    {
        $this->sendGetRequest('/organizer/Org');
        $contentsData = $this->getResponseContents()['data'];

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(self::ORGANIZER_NAME_FIRST, $contentsData['name']);
        $this->assertEquals(self::ORGANIZER_DESCRIPTION_FIRST, $contentsData['description']);
    }
}
