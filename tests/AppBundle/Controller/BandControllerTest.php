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
    const BAND_DESCRIPTION_SECOND = 'Derband description.';
    const BAND_USER_LOGIN_FIRST = 'bander';
    const BAND_USER_LOGIN_SECOND = 'derban';

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
    public function listAction_GETBandRequest_bandCreated()
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

        $this->assertEquals(200, $createBandResponseCode);

        $this->sendGetRequest('/bands');
        $listBandsResponseCode = $this->getResponseCode();
        $bandListContents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertEquals(self::BAND_NAME_SECOND, $bandListContents['data'][1]['name']);
        $this->assertEquals(self::BAND_DESCRIPTION_SECOND, $bandListContents['data'][1]['description']);
        $this->assertContains(self::BAND_USER_LOGIN_FIRST, $bandListContents['data'][1]['users']);
        $this->assertContains(self::BAND_USER_LOGIN_SECOND, $bandListContents['data'][1]['users']);
    }
}
