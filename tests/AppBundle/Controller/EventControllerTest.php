<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Fixture\EventFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class EventControllerTest extends FunctionalTester
{

    const EVENT_NAME_FIRST = 'first event';
    const EVENT_NAME_SECOND = 'first renamed event';
    const EVENT_DATE_FIRST = '2000-08-08 18:18:00';
    const EVENT_DESCRIPTION_FIRST = 'first event description';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
                EventFixture::class,
            ]
        );
        parent::setUp();
    }

    /** @test */
    public function createAction_POSTEventEmptyRequest_validationErrors()
    {
        $this->sendPostRequest('/event', []);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'];

        $this->assertEquals(400, $responseCode);
        $this->assertContains('Parameter is mandatory: name.', $errors);
        $this->assertContains('Parameter is mandatory: date.', $errors);
        $this->assertContains('Parameter is mandatory: description.', $errors);
    }

    /** @test */
    public function createAction_POSTEventWithNameAndDateAndDescriptionRequest_eventCretedAndSavedToDbAndLocationReturned(
    )
    {
        $createEventData = [
            'name'        => self::EVENT_NAME_FIRST,
            'date'        => self::EVENT_DATE_FIRST,
            'description' => self::EVENT_DESCRIPTION_FIRST,
        ];

        $this->sendPostRequest('/event', $createEventData);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'] ?? [];

        $this->assertEquals(201, $responseCode);
        $this->assertEmpty($errors);

        $resourceLocation = $this->getResponseLocation();
        $this->sendGetRequest($resourceLocation);
        $responseCode = $this->getResponseCode();
        $responseData = $this->getResponseContents()['data'];

        $this->assertEquals(200, $responseCode);
        $this->assertEquals($createEventData['name'], $responseData['name']);
        $this->assertEquals($createEventData['date'], $responseData['date']);
        $this->assertEquals($createEventData['description'], $responseData['description']);
    }

    /** @test */
    public function editAction_PUTEventIdEmptyParameters_validationErrors()
    {
        $this->sendPutRequest('/event/1', []);
        $responseCode = $this->getResponseCode();

        $this->assertEquals(400, $responseCode);
    }

    /** @test */
    public function editAction_PUTEventIdNameParameter_eventWithGivenIdChangedNameAndSavedToDb()
    {
        $parameters = [
            'name'        => self::EVENT_NAME_SECOND,
            'date'        => self::EVENT_DATE_FIRST,
            'description' => self::EVENT_DESCRIPTION_FIRST,
        ];

        $this->sendPutRequest('/event/1', $parameters);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'] ?? [];

        $this->assertEquals(204, $responseCode);
        $this->assertEmpty($errors);

        $this->sendGetRequest('/event/1');
        $responseCode = $this->getResponseCode();
        $responseData = $this->getResponseContents()['data'];

        $this->assertEquals(200, $responseCode);
        $this->assertEquals($parameters['name'], $responseData['name']);
        $this->assertEquals($parameters['date'], $responseData['date']);
        $this->assertEquals($parameters['description'], $responseData['description']);
    }
}
