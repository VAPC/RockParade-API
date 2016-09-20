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
    const USER_CREATOR_LOGIN = 'first';
    const USER_SECOND_LOGIN = 'second';
    const USER_THIRD_LOGIN = 'third';
    const ORGANIZER_NAME_SECOND = 'first organizer name';
    const ORGANIZER_DESCRIPTION_SECOND = 'first organizer description';
    const ORGANIZER_MEMBER_FIRST_SHORT_DESCRIPTION = 'first short description';
    const ORGANIZER_MEMBER_SECOND_SHORT_DESCRIPTION = 'second short description';
    const ORGANIZER_MEMBER_THIRD_SHORT_DESCRIPTION = 'third short description';
    const ORGANIZER_MEMBER_THIRD_DESCRIPTION = 'third description';

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
    public function viewAction_GETOrganizerUnexistingIdRequest_organizerResource()
    {
        $this->sendGetRequest('/organizer/unexisting');
        $contents = $this->getResponseContents();

        $this->assertEquals(404, $this->getResponseCode());
        $this->assertContains('Organizer "unexisting" was not found.', $contents['errors']);
    }

    /** @test */
    public function viewAction_GETOrganizerExistingIdRequest_organizerResource()
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
    public function createAction_POSTOrganizerRequestWithExistingName_validationError()
    {
        $parameters = [
            'name' => self::ORGANIZER_NAME_FIRST,
        ];

        $this->sendPostRequest('/organizer', $parameters);
        $errors = $this->getResponseContents()['errors'];

        $this->assertEquals(400, $this->getResponseCode());
        $this->assertContains('Organizer with name "Org" already exists.', $errors);
    }

    /** @test */
    public function createAction_POSTOrganizerRequestWithNameAndDescriptionData_organizerCreatedAndCreatorAddedAsMemberAndLocation()
    {
        $parameters = [
            'name'        => self::ORGANIZER_NAME_SECOND,
            'description' => self::ORGANIZER_DESCRIPTION_SECOND,
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
        $this->assertEquals(self::USER_CREATOR_LOGIN, $contentsData['members'][0]['login']);
    }

    /** @test */
    public function createAction_POSTOrganizerRequestWithMembersData_organizerCreatedWithMembers()
    {
        $parameters = [
            'name'        => self::ORGANIZER_NAME_SECOND,
            'description' => self::ORGANIZER_DESCRIPTION_SECOND,
            'members'     => [
                [
                    'login'             => self::USER_SECOND_LOGIN,
                    'short_description' => self::ORGANIZER_MEMBER_SECOND_SHORT_DESCRIPTION,
                ],
                [
                    'login'             => self::USER_THIRD_LOGIN,
                    'short_description' => self::ORGANIZER_MEMBER_THIRD_SHORT_DESCRIPTION,
                    'description'       => self::ORGANIZER_MEMBER_THIRD_DESCRIPTION,
                ],
            ],
        ];

        $this->sendPostRequest('/organizer', $parameters);
        $this->assertEquals(201, $this->getResponseCode());

        $this->sendGetRequest($this->getResponseLocation());
        $contentsData = $this->getResponseContents()['data'];
        $this->assertEquals(self::USER_CREATOR_LOGIN, $contentsData['members'][0]['login']);
        $this->assertEquals(self::USER_SECOND_LOGIN, $contentsData['members'][1]['login']);
        $this->assertEquals(self::USER_THIRD_LOGIN, $contentsData['members'][2]['login']);
        $this->assertEquals(self::ORGANIZER_MEMBER_THIRD_SHORT_DESCRIPTION, $contentsData['members'][2]['short_description']);
        $this->assertEquals(self::ORGANIZER_MEMBER_THIRD_DESCRIPTION, $contentsData['members'][2]['description']);
    }

    /** @test */
    public function createMemberAction_POSTOrganizerIdMembersEmptyRequest_validationError()
    {
        $this->sendPostRequest(sprintf('/organizer/members', self::ORGANIZER_ID_FIRST));
        $errors = $this->getResponseContents()['errors'];

        $this->assertEquals(400, $this->getResponseCode());
        $this->assertContains('Parameter \'ambassador\' is mandatory.', $errors);
        $this->assertContains('Parameter \'login\' is mandatory.', $errors);
        $this->assertContains('Parameter \'short_description\' is mandatory.', $errors);
    }

    /** @test */
    public function createMemberAction_POSTOrganizerIdMembersRequest_organizerMemberCreated()
    {
        $parameters = [
            'ambassador'        => self::ORGANIZER_ID_FIRST,
            'login'             => self::USER_SECOND_LOGIN,
            'short_description' => self::ORGANIZER_MEMBER_FIRST_SHORT_DESCRIPTION,
        ];

        $this->sendPostRequest(sprintf('/organizer/members', self::ORGANIZER_ID_FIRST), $parameters);

        $this->assertEquals(201, $this->getResponseCode());
    }
}
