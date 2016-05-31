<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Fixture\BandFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class BandControllerTest extends FunctionalTester
{
    const BAND_NAME = 'Test Band';
    const BAND_DESCRIPTION = 'Band description.';
    const BAND_USER_LOGIN = 'Banders';

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
        $this->assertEquals(self::BAND_USER_LOGIN, $contents['data'][0]['name']);
        $this->assertEquals(self::BAND_DESCRIPTION, $contents['data'][0]['description']);
    }

    /** @test */
    public function createAction_POSTBandCreateRequest_bandCreated()
    {
        $this->followRedirects();
        $parameters = [
            'name'        => self::BAND_NAME,
            'description' => self::BAND_DESCRIPTION,
            'users'       => [
                'first',
                'second',
            ],
        ];

        $this->sendPostRequest('/band/create', $parameters);
        $createBandResponseCode = $this->getResponseCode();

        $this->assertEquals(200, $createBandResponseCode);

        $this->sendGetRequest('/bands');
        $listBandsResponseCode = $this->getResponseCode();
        $bandListContents = $this->getResponseContents();

        $this->assertEquals(200, $listBandsResponseCode);
        $this->assertEquals(self::BAND_USER_LOGIN, $bandListContents['data'][0]['name']);
        $this->assertEquals(self::BAND_DESCRIPTION, $bandListContents['data'][0]['description']);
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
}
