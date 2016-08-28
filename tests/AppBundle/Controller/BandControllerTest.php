<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Fixture\BandFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class BandControllerTest extends FunctionalTester
{
    const BAND_NAME_FIRST = 'Banders';
    const BAND_NAME_FIRST_EDITED = 'New Derbans';
    const BAND_NAME_SECOND = 'Derbans';
    const BAND_NAME_EXISTING = 'Existing Band';
    const BAND_DESCRIPTION_FIRST = 'Band description.';
    const BAND_DESCRIPTION_FIRST_EDITED = 'New Derbans description.';
    const BAND_DESCRIPTION_SECOND = 'Derband description.';
    const BAND_USER_LOGIN_FIRST = 'bander';
    const BAND_USER_LOGIN_SECOND = 'derban';
    const BAND_USER_LOGIN_THIRD = 'rocker';
    const USER_DESCRIPTION_SHORT_FIRST = 'first description';
    const USER_DESCRIPTION_SHORT_SECOND = 'hard rocker guitarist';
    const USER_DESCRIPTION_FIRST = 'Long description of first user';
    const USER_DESCRIPTION_SECOND = 'Hard rocker was the second musician in this band.';
    const BAND_MEMBER_FIRST_SHORT_DESCRIPTION = 'bass guitar';
    const BAND_MEMBER_FIRST_DESCRIPTION = 'loremus unitus';
    const BAND_MEMBER_SECOND_DESCRIPTION = 'secondus shortus';
    const BAND_MEMBER_SECOND_SHORT_DESCRIPTION = 'violin';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
                BandFixture::class,
            ]
        );
        parent::setUp();
    }

    /** @test */
    public function listAction_GETBandsRequest_listAllBands()
    {
        $this->followRedirects();

        $this->sendGetRequest('/bands');
        $listBandsResponseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertEquals(self::BAND_NAME_FIRST, $contents['data'][0]['name']);
        $this->assertEquals(self::BAND_DESCRIPTION_FIRST, $contents['data'][0]['description']);
        $this->assertTrue(array_key_exists('total', $contents));
        $this->assertTrue(array_key_exists('limit', $contents));
        $this->assertTrue(array_key_exists('offset', $contents));
    }

    /** @test */
    public function createAction_POSTBandCreateEmptyRequest_validationErrors()
    {
        $this->sendPostRequest('/band', []);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'];

        $this->assertEquals(400, $responseCode);
        $this->assertContains('Parameter is mandatory: name.', $errors);
        $this->assertContains('Parameter is mandatory: description.', $errors);
    }

    /** @test */
    public function createAction_POSTBandCreateRequest_bandCreated()
    {
        $this->followRedirects();
        $parameters = [
            'name'        => self::BAND_NAME_SECOND,
            'description' => self::BAND_DESCRIPTION_SECOND,
            'members'     => [
                [
                    'login'             => self::BAND_USER_LOGIN_FIRST,
                    'short_description' => self::USER_DESCRIPTION_SHORT_FIRST,
                ],
                [
                    'login'             => self::BAND_USER_LOGIN_SECOND,
                    'short_description' => self::USER_DESCRIPTION_SHORT_SECOND,
                ],
            ],
        ];

        $this->sendPostRequest('/band', $parameters);
        $createBandResponseCode = $this->getResponseCode();
        $createBandResponseLocation = $this->getResponseLocation();

        $this->assertEquals(201, $createBandResponseCode);
        $this->assertEquals('/band/Derbans', $createBandResponseLocation);

        $this->sendGetRequest('/bands');
        $listBandsResponseCode = $this->getResponseCode();
        $bandListContents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertEquals(self::BAND_NAME_SECOND, $bandListContents['data'][2]['name']);
        $this->assertEquals(self::BAND_DESCRIPTION_SECOND, $bandListContents['data'][2]['description']);
        $this->assertContains(self::BAND_USER_LOGIN_FIRST, $bandListContents['data'][2]['members'][0]['login']);
        $this->assertContains(self::USER_DESCRIPTION_SHORT_FIRST, $bandListContents['data'][2]['members'][0]['short_description']);
        $this->assertContains(self::BAND_USER_LOGIN_SECOND, $bandListContents['data'][2]['members'][1]['login']);
        $this->assertContains(self::USER_DESCRIPTION_SHORT_SECOND, $bandListContents['data'][2]['members'][1]['short_description']);
    }

    /** @test */
    public function viewAction_GETBandNameRequest_singleBandInfo()
    {
        $this->sendGetRequest('/band/Banders');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::BAND_NAME_FIRST, $contents['data']['name']);
        $this->assertEquals(self::BAND_DESCRIPTION_FIRST, $contents['data']['description']);
    }

    /** @test */
    public function viewAction_GETBandNotExistingNameRequest_bandNotFoundError()
    {
        $this->sendGetRequest('/band/VeryUnexistingBand');
        $contents = $this->getResponseContents();
        $responseCode = $this->getResponseCode();

        $this->assertEquals(404, $responseCode);
        $this->assertContains('Band with name "VeryUnexistingBand" was not found.', $contents['errors']);
    }

    /** @test */
    public function editAction_PUTBandNameRequestWithExistingName_validationError()
    {
        $this->followRedirects();
        $parameters = [
            'name'        => self::BAND_NAME_EXISTING,
            'description' => self::BAND_DESCRIPTION_FIRST,
            'members'     => [
                self::BAND_USER_LOGIN_THIRD,
            ],
        ];

        $this->sendPutRequest('/band/Banders', $parameters);
        $contents = $this->getResponseContents();

        $this->assertEquals(400, $this->getResponseCode());
        $this->assertContains('Band with name "Existing Band" already exists.', $contents['errors']);
    }

    /** @test */
    public function editAction_PUTBandNameRequestWithNewParameters_bandUpdatedWithNewParameters()
    {
        $this->followRedirects();
        $parameters = [
            'name'        => self::BAND_NAME_FIRST_EDITED,
            'description' => self::BAND_DESCRIPTION_FIRST_EDITED,
            'members'     => [
                [
                    'login'             => self::BAND_USER_LOGIN_THIRD,
                    'short_description' => self::USER_DESCRIPTION_SHORT_SECOND,
                ],
            ],
        ];

        $this->sendPutRequest('/band/Banders', $parameters);
        $this->assertEquals(204, $this->getResponseCode());

        $this->sendGetRequest('/band/Banders');
        $this->assertEquals(404, $this->getResponseCode());

        $this->sendGetRequest('/band/New%20Derbans');
        $contents = $this->getResponseContents();
        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(self::BAND_NAME_FIRST_EDITED, $contents['data']['name']);
        $this->assertEquals(self::BAND_DESCRIPTION_FIRST_EDITED, $contents['data']['description']);
    }

    /** @test */
    public function listMembersAction_GETBandMembers_listBandMembers()
    {
        $this->followRedirects();

        $this->sendGetRequest('/band/Banders/members');
        $listBandsResponseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertEquals(self::BAND_USER_LOGIN_FIRST, $contents['data'][0]['login']);
        $this->assertEquals(self::BAND_MEMBER_FIRST_DESCRIPTION, $contents['data'][0]['description']);
        $this->assertEquals(self::BAND_MEMBER_FIRST_SHORT_DESCRIPTION, $contents['data'][0]['short_description']);
    }

    /** @test */
    public function addMemberAction_POSTBandNameMembersRequestWithNewMember_bandMemberAdded()
    {
        $parameters = [
            'login'             => self::BAND_USER_LOGIN_SECOND,
            'short_description' => self::USER_DESCRIPTION_SHORT_SECOND,
            'description'       => self::USER_DESCRIPTION_SECOND,
        ];

        $this->sendPostRequest('/band/Banders/members', $parameters);
        $this->assertEquals(200, $this->getResponseCode());

        $this->sendGetRequest('/band/Banders/members');
        $contents = $this->getResponseContents();
        $this->assertEquals('derban', $contents['data'][1]['login']);
        $this->assertEquals('hard rocker guitarist', $contents['data'][1]['short_description']);
    }
    
    /** @test */
    public function deleteMemberAction_DELETEBandNameMemberLoginRequest_bandMemberDeleted()
    {
        $this->sendDeleteRequest('/band/Banders/member/bander');
        $this->assertEquals(204, $this->getResponseCode());

        $this->sendGetRequest('/band/Banders/members');
        $contents = $this->getResponseContents();
        $this->assertEmpty($contents['data']);
    }
    
    /** @test */
    public function updateMemberAction_PUTBandNameMemberLoginRequest_bandMemberUpdatedWithNewParameters()
    {
        $this->followRedirects();
        $parameters = [
            'short_description' => self::BAND_MEMBER_SECOND_SHORT_DESCRIPTION,
            'description'       => self::BAND_MEMBER_SECOND_DESCRIPTION,
        ];

        $this->sendGetRequest('/band/Banders/members');
        $contents = $this->getResponseContents();
        $this->assertEquals(self::BAND_USER_LOGIN_FIRST, $contents['data'][0]['login']);
        $this->assertEquals(self::BAND_MEMBER_FIRST_DESCRIPTION, $contents['data'][0]['description']);
        $this->assertEquals(self::BAND_MEMBER_FIRST_SHORT_DESCRIPTION, $contents['data'][0]['short_description']);

        $this->sendPutRequest('/band/Banders/member/bander', $parameters);
        $this->assertEquals(204, $this->getResponseCode());

        $this->sendGetRequest('/band/Banders/members');
        $contents = $this->getResponseContents();
        $this->assertEquals(self::BAND_USER_LOGIN_FIRST, $contents['data'][0]['login']);
        $this->assertEquals(self::BAND_MEMBER_SECOND_DESCRIPTION, $contents['data'][0]['description']);
        $this->assertEquals(self::BAND_MEMBER_SECOND_SHORT_DESCRIPTION, $contents['data'][0]['short_description']);

    }
}
