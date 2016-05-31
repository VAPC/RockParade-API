<?php

namespace Tests\AppBundle\Controller;

use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class BandControllerTest extends FunctionalTester
{
    const BAND_NAME = 'Test Band';
    const DESCRIPTION = 'description';

    /** @test */
    public function createAction_POSTBandCreateRequest_bandCreated()
    {
        $parameters = [
            'name'        => self::BAND_NAME,
            'description' => self::DESCRIPTION,
            'users'       => [
                'first',
                'second',
            ],
        ];

        $this->sendPostRequest('/band/create', $parameters);
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
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
