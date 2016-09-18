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

    const ORGANIZER_ID_FIRST = 'test-organizer';
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
        $this->sendGetRequest(sprintf('/organizer/%s', self::ORGANIZER_ID_FIRST));
        $contentsData = $this->getResponseContents()['data'];

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(self::ORGANIZER_NAME_FIRST, $contentsData['name']);
        $this->assertEquals(self::ORGANIZER_DESCRIPTION_FIRST, $contentsData['description']);
    }

    /** @test */
    public function createAction_POSTOrganizerRequestWithEmptyData_validationErrors()
    {
        $this->sendPostRequest('/organizer');

        $this->assertEquals(400, $this->getResponseCode());
    }

    /** @test */
    public function createAction_POSTOrganizerRequestWithNameAndDescriptionData_organizerCreatedAndLocationReturned()
    {
        $parameters = [
            'name'        => 'first organizer name',
            'description' => 'first organizer description',
        ];

        $this->sendPostRequest('/organizer', $parameters);
        $responseLocation = $this->getResponseLocation();

        $this->assertEquals(201, $this->getResponseCode());
        $this->assertRegExp('/^http.?:\/\/.*organizer\/..*$/', $responseLocation);

        $this->sendGetRequest($responseLocation);
        $contentsData = $this->getResponseContents()['data'];

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(8, strlen($contentsData['id']));
        $this->assertEquals($parameters['name'], $contentsData['name']);
        $this->assertEquals($parameters['description'], $contentsData['description']);
    }
    
    /** @test */
    public function createMemberAction_POSTOrganizerIdMembersEmptyRequest_validationError()
    {
        $this->sendPostRequest(sprintf('/organizer/%s/members', self::ORGANIZER_ID_FIRST));
        $contents = $this->getResponseContents();

        $this->assertEquals(400, $this->getResponseCode());
        $this->assertContains('Parameter \'login\' is mandatory', $contents['errors']);
        $this->assertContains('Parameter \'short_description\' is mandatory', $contents['errors']);
    }
}
