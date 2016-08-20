<?php

namespace Tests\AppBundle\Controller;

use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class EventControllerTest extends FunctionalTester
{

    const EVENT_NAME_FIRST = 'first2 event';
    const EVENT_DATE_FIRST = '2004-07-24 18:18:18';
    const EVENT_DESCRIPTION_FIRST = 'first event description';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures([]);
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
    public function createAction_POSTEventWithNameAndDateAndDescriptionRequest_eventCreatedAndLocationReturned()
    {
        $createEventData = [
            'name' => self::EVENT_NAME_FIRST,
            'date' => self::EVENT_DATE_FIRST,
            'description' => self::EVENT_DESCRIPTION_FIRST,
        ];

        $this->sendPostRequest('/event', $createEventData);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'] ?? [];

        $this->assertEquals(201, $responseCode);
        $this->assertEmpty($errors);
    }
}
