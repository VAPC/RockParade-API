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
    const BAND_DESCRIPTION_FIRST = 'Band description.';
    const BAND_NAME_SECOND = 'Derbans';
    const BAND_NAME_FIRST_EDITED = 'New Derbans';
    const BAND_DESCRIPTION_SECOND = 'Derband description.';
    const BAND_DESCRIPTION_FIRST_EDITED = 'New Derbans description.';
    const BAND_USER_LOGIN_FIRST = 'bander';
    const BAND_USER_LOGIN_SECOND = 'derban';
    const BAND_USER_LOGIN_THIRD = 'rocker';

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
        $this->sendPostRequest('/band/create', []);
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
            'users'       => [
                self::BAND_USER_LOGIN_FIRST,
                self::BAND_USER_LOGIN_SECOND,
            ],
        ];

        $this->sendPostRequest('/band/create', $parameters);
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
        $this->assertContains(self::BAND_USER_LOGIN_FIRST, $bandListContents['data'][2]['users']);
        $this->assertContains(self::BAND_USER_LOGIN_SECOND, $bandListContents['data'][2]['users']);
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
}
