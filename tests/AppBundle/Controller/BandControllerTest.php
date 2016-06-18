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
    const USER_DESCRIPTION_SHORT = 'hard rocker guitarist';
    const USER_DESCRIPTION = 'Hard rocker was the second musician in this band.';
    const BAND_MEMBER_FIRST_SHORT_DESCRIPTION = 'bass guitar';
    const BAND_MEMBER_FIRST_DESCRIPTION = 'loremus unitus';

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
    }

    /** @test */
    public function createAction_POSTBandCreateEmptyRequest_validationErrors()
    {
        $this->sendPostRequest('/band', []);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'];

        $this->assertEquals(400, $responseCode);
        $this->assertContains('Parameter \'name\' is mandatory', $errors);
        $this->assertContains('Parameter \'description\' is mandatory', $errors);
    }

    /** @test */
    public function createAction_POSTBandCreateRequest_bandCreated()
    {
        $this->followRedirects();
        $parameters = [
            'name'        => self::BAND_NAME_SECOND,
            'description' => self::BAND_DESCRIPTION_SECOND,
            'members'     => [
                self::BAND_USER_LOGIN_FIRST,
                self::BAND_USER_LOGIN_SECOND,
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
        $this->assertContains(self::BAND_USER_LOGIN_FIRST, $bandListContents['data'][2]['members']);
        $this->assertContains(self::BAND_USER_LOGIN_SECOND, $bandListContents['data'][2]['members']);
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
                self::BAND_USER_LOGIN_THIRD,
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
    public function getMembers_GETBandMembers_listBandMembers()
    {
        $this->followRedirects();

        $this->sendGetRequest('/band/Banders/members');
        $listBandsResponseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertEquals(self::BAND_USER_LOGIN_FIRST, $contents['data'][0]['user']);
        $this->assertEquals(self::BAND_MEMBER_FIRST_DESCRIPTION, $contents['data'][0]['description']);
        $this->assertEquals(self::BAND_MEMBER_FIRST_SHORT_DESCRIPTION, $contents['data'][0]['short_description']);
    }

    /** @test */
    public function addMember_POSTBandMembersWithNewMember_bandMemberAdded()
    {
        $parameters = [
            'user'              => self::BAND_USER_LOGIN_SECOND,
            'short_description' => self::USER_DESCRIPTION_SHORT,
            'description'       => self::USER_DESCRIPTION,
        ];

        $this->sendPostRequest('/band/Banders/members', $parameters);
        $this->assertEquals(200, $this->getResponseCode());

        $this->sendGetRequest('/band/Banders/members');
        $contents = $this->getResponseContents();
        $this->assertEquals('derban', $contents['data'][1]['user']);
        $this->assertEquals('hard rocker guitarist', $contents['data'][1]['short_description']);
    }

}
