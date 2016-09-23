<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\Image;
use AppBundle\Entity\Link;
use AppBundle\Fixture\EventFixture;
use AppBundle\Fixture\UserFixture;
use Tests\FunctionalTester;

/**
 * @author Vehsamrak
 */
class EventControllerTest extends FunctionalTester
{

    const EVENT_NAME_FIRST = 'first event';
    const EVENT_NAME_SECOND = 'first renamed event';
    const EVENT_DATE_FIRST = '2000-08-08 18:18';
    const EVENT_DESCRIPTION_FIRST = 'first event description';
    const EVENT_NAME_FIXTURE_FIRST = 'Test Event';
    const USER_LOGIN_EXECUTOR = 'first';
    const IMAGE_NAME = 'test image';
    const IMAGE_BASE64_CONTENT = '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';
    const VALID_URL_FIRST = 'http://valid-url.net';
    const URL_DESCRIPTION_FIRST = 'url description';
    const VALID_URL_SECOND = 'http://second.net';
    const URL_DESCRIPTION_SECOND = 'description second';
    const EVENT_PLACE_FIRST = 'First event place';

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->loadFixtures(
            [
                UserFixture::class,
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
        $this->assertContains('Parameter is mandatory: date (yyyy-MM-dd HH:mm).', $errors);
        $this->assertContains('Parameter is mandatory: description.', $errors);
        $this->assertContains('Parameter is mandatory: place.', $errors);
    }

    /** @test */
    public function createAction_POSTEventWithNameAndDateAndDescriptionRequest_eventCretedAndSavedToDbAndLocationReturned()
    {
        $createEventData = [
            'name'        => self::EVENT_NAME_FIRST,
            'date'        => self::EVENT_DATE_FIRST,
            'description' => self::EVENT_DESCRIPTION_FIRST,
            'place'       => self::EVENT_PLACE_FIRST,
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
        $this->assertEquals($createEventData['place'], $responseData['place']);
        $this->assertEquals(self::USER_LOGIN_EXECUTOR, $responseData['creator']);
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
        $existingEvent = $this->getFixtureEvent();
        $existingEventId = $existingEvent->getId();

        $parameters = [
            'name'        => self::EVENT_NAME_SECOND,
            'date'        => self::EVENT_DATE_FIRST,
            'description' => self::EVENT_DESCRIPTION_FIRST,
            'place'       => self::EVENT_PLACE_FIRST,
        ];

        $this->sendPutRequest(sprintf('/event/%s', $existingEventId), $parameters);
        $responseCode = $this->getResponseCode();
        $errors = $this->getResponseContents()['errors'] ?? [];

        $this->assertEquals(204, $responseCode);
        $this->assertEmpty($errors);

        $this->sendGetRequest(sprintf('/event/%s', $existingEventId));
        $responseCode = $this->getResponseCode();
        $responseData = $this->getResponseContents()['data'];

        $this->assertEquals(200, $responseCode);
        $this->assertEquals($parameters['name'], $responseData['name']);
        $this->assertEquals($parameters['date'], $responseData['date']);
        $this->assertEquals($parameters['description'], $responseData['description']);
    }
    
    /** @test */
    public function deleteAction_DELETEEventIdRequest_eventDeletedFromDb()
    {
        $existingEvent = $this->getFixtureEvent();
        $existingEventId = $existingEvent->getId();

        $this->sendDeleteRequest(sprintf('/event/%s', $existingEventId));
        $this->assertEquals(204, $this->getResponseCode());

        $this->sendGetRequest(sprintf('/event/%s', $existingEventId));
        $contents = $this->getResponseContents();
        $this->assertContains(sprintf('Event "%s" was not found.', $existingEventId), $contents['errors']);
    }

    /** @test */
    public function listAction_GETRequest_listOfEventsReturned()
    {
        $this->sendGetRequest('/events');
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertTrue(array_key_exists('data', $contents));
        $this->assertTrue(array_key_exists('limit', $contents));
        $this->assertTrue(array_key_exists('offset', $contents));
        $this->assertTrue(array_key_exists('total', $contents));
    }

    /** @test */
    public function findLikeAction_GETEventsLikeTestRequest_listOfFoundEventsReturned()
    {
        $this->sendGetRequest('/events/like/test');
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(2, $contents['total']);
        $this->assertCount(2, $contents['data']);
    }

    /** @test */
    public function findLikeAction_GETEventsLikeFoobarRequest_noEventsReturned()
    {
        $this->sendGetRequest('/events/like/foobar');
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(0, $contents['total']);
        $this->assertCount(0, $contents['data']);
    }

    /** @test */
    public function findLikeAction_GETEventsLikeTestLimit1Request_oneEventReturned()
    {
        $this->sendGetRequest('/events/like/test/1');
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(2, $contents['total']);
        $this->assertEquals(1, $contents['limit']);
        $this->assertEquals(0, $contents['offset']);
        $this->assertCount(1, $contents['data']);
    }

    /** @test */
    public function findLikeAction_GETEventsLikeTestLimitNullOffset1Request_oneEventReturned()
    {
        $this->sendGetRequest('/events/like/test/null/1');
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertEquals(2, $contents['total']);
        $this->assertEquals(50, $contents['limit']);
        $this->assertEquals(1, $contents['offset']);
        $this->assertCount(1, $contents['data']);
    }

    /** @test */
    public function addImageAction_POSTEventIdImageRequestWithUnexistingEventId_validationErrorReturned()
    {
        $this->sendPostRequest('/event/-1/image');
        $contents = $this->getResponseContents();

        $this->assertEquals(404, $this->getResponseCode());
        $this->assertContains('Event with id "-1" was not found.', $contents['errors']);
    }

    /** @test */
    public function addImageAction_POSTEventIdImageRequestWithEmptyParameters_validationErrorReturned()
    {
        $event = $this->getFixtureEvent();

        $this->sendPostRequest(sprintf('/event/%s/image', $event->getId()));
        $contents = $this->getResponseContents();

        $this->assertEquals(400, $this->getResponseCode());
        $this->assertContains('Parameters are mandatory: image[name] and image[content].', $contents['errors']);
    }

    /** @test */
    public function addImageAction_POSTEventIdImageRequestWithBase64ImageAndExecutorIsEventCreator_imageSavedAndOKReturned()
    {
        $event = $this->getFixtureEvent();
        $eventId = $event->getId();
        $requestString = sprintf('/event/%s/image', $eventId);
        $parameters = [
            'image' => [
                'name'    => self::IMAGE_NAME,
                'content' => self::IMAGE_BASE64_CONTENT,
            ],
        ];

        $this->sendPostRequest($requestString, $parameters);
        $responseLocation = $this->getResponseLocation();
        $createdImage = $this->getLastImage();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertRegExp(
            sprintf(
                '/^http(.*)event\/%s\/image\/%s$/',
                $eventId,
                rawurlencode($createdImage->getName())
            ),
            $responseLocation
        );

        $this->sendGetRequest($responseLocation);

        $this->assertEquals(200, $this->getResponseCode());
        $this->deleteUploadedFile();
    }

    /** @test */
    public function addImageAction_POSTEventIdImageRequestWithBase64ImageAndExecutorIsNotEventCreator_accessDenied()
    {
        $this->givenExecutorNotEventCreator();
        $event = $this->getFixtureEvent();
        $eventId = $event->getId();
        $requestString = sprintf('/event/%s/image', $eventId);
        $parameters = [
            'image' => [
                'name'    => self::IMAGE_NAME,
                'content' => self::IMAGE_BASE64_CONTENT,
            ],
        ];

        $this->sendPostRequest($requestString, $parameters);
        $contents = $this->getResponseContents();

        $this->assertEquals(403, $this->getResponseCode());
        $this->assertContains('Only event creator can add images.', $contents['errors']);
    }

    /** @test */
    public function deleteImageAction_DELETEEventIdImageNameRequestAndExecutorIsEventCreator_imageDeleted()
    {
        $event = $this->getFixtureEvent();
        $eventId = $event->getId();
        /** @var Image $eventImage */
        $eventImage = $event->getImages()->first();
        $eventImageName = $eventImage->getName();
        $requestString = sprintf('/event/%s/image/%s', $eventId, $eventImageName);

        $this->sendDeleteRequest($requestString);

        $this->assertEquals(200, $this->getResponseCode());
    }

    /** @test */
    public function deleteImageAction_DELETEEventIdImageNameRequestAndExecutorIsNotEventCreator_accessDenied()
    {
        $this->givenExecutorNotEventCreator();
        $event = $this->getFixtureEvent();
        $eventId = $event->getId();
        /** @var Image $eventImage */
        $eventImage = $event->getImages()->first();
        $eventImageName = $eventImage->getName();
        $requestString = sprintf('/event/%s/image/%s', $eventId, $eventImageName);

        $this->sendDeleteRequest($requestString);
        $contents = $this->getResponseContents();

        $this->assertEquals(403, $this->getResponseCode());
        $this->assertContains('Only event creator can delete images.', $contents['errors']);
    }

    /** @test */
    public function viewAction_GETEventId_eventDetailsReturned()
    {
        $event = $this->getFixtureEvent();
        $eventId = $event->getId();
        $requestString = sprintf('/event/%s', $eventId);

        $this->sendGetRequest($requestString);
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertCount(1, $contents['data']['images']);
        $this->assertEquals(self::USER_LOGIN_EXECUTOR, $contents['data']['creator']);
        $this->assertCount(1, $contents['data']['links']);
    }

    /** @test */
    public function addLinksAction_POSTEventIdLinksAndEmptyRequest_validationError()
    {
        $eventId = $this->getFixtureEventId();
        $requestString = sprintf('/event/%s/links', $eventId);

        $this->sendPostRequest($requestString);
        $contents = $this->getResponseContents();

        $this->assertEquals(400, $this->getResponseCode());
        $this->assertContains('Parameter is mandatory: links.', $contents['errors']);
    }

    /** @test */
    public function addLinksAction_POSTEventIdLinksAndRequestWithLinkUrlAndExecutorIsNotEventCreator_accessDenied()
    {
        $this->givenExecutorNotEventCreator();
        $eventId = $this->getFixtureEventId();
        $requestString = sprintf('/event/%s/links', $eventId);
        $parameters = [
            'links' => [
                [
                    'url' => self::VALID_URL_FIRST,
                ],
            ],
        ];

        $this->sendPostRequest($requestString, $parameters);
        $contents = $this->getResponseContents();

        $this->assertEquals(403, $this->getResponseCode());
        $this->assertContains('Only event creator can add links.', $contents['errors']);
    }

    /** @test */
    public function addLinksAction_POSTEventIdLinksAndRequestWithLinkUrlAndExecutorIsEventCreator_linksSavedToDatabase()
    {
        $eventId = $this->getFixtureEventId();
        $requestString = sprintf('/event/%s/links', $eventId);
        $parameters = [
            'links' => [
                [
                    'url'         => self::VALID_URL_FIRST,
                    'description' => self::URL_DESCRIPTION_FIRST,
                ],
                [
                    'url'         => self::VALID_URL_SECOND,
                    'description' => self::URL_DESCRIPTION_SECOND,
                ],
            ],
        ];

        $this->sendPostRequest($requestString, $parameters);
        $responseLocation = $this->getResponseLocation();

        $this->assertEquals(201, $this->getResponseCode());

        $this->sendGetRequest($responseLocation);
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertCount(3, $contents['data']['links']);
    }

    /** @test */
    public function deleteLinksAction_DELETEEventIdLinkUrlAndExecutorIsNotEventCreator_accessDenied()
    {
        $this->givenExecutorNotEventCreator();
        $event = $this->getFixtureEvent();
        /** @var Link $link */
        $link = $event->getLinks()->first();
        $requestString = sprintf('/event/%s/link/%s', $event->getId(), $link->getId());

        $this->sendDeleteRequest($requestString);
        $contents = $this->getResponseContents();

        $this->assertEquals(403, $this->getResponseCode());
        $this->assertContains('Only event creator can delete links.', $contents['errors']);
    }

    /** @test */
    public function deleteLinksAction_DELETEEventIdLinkUrlAndExecutorIsEventCreator_linkDeleted()
    {
        $event = $this->getFixtureEvent();
        /** @var Link $link */
        $link = $event->getLinks()->first();
        $eventId = $event->getId();
        $requestString = sprintf('/event/%s/link/%s', $eventId, $link->getId());

        $this->sendGetRequest(sprintf('/event/%s', $eventId));
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertCount(1, $contents['data']['links']);

        $this->sendDeleteRequest($requestString);

        $this->assertEquals(200, $this->getResponseCode());

        $this->sendGetRequest(sprintf('/event/%s', $eventId));
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertCount(0, $contents['data']['links']);
    }

    private function getFixtureEvent(): Event
    {
        return $this->getContainer()
                    ->get('rockparade.event_repository')
                    ->findLike(self::EVENT_NAME_FIXTURE_FIRST)
                    ->first();
    }

    private function getFixtureEventId(): string
    {
        return $this->getFixtureEvent()->getId();
    }

    private function getLastImage(): Image
    {
        $allImages = $this->getContainer()
                          ->get('rockparade.image_repository')
                          ->findAll();

        return array_pop($allImages);
    }

    private function deleteUploadedFile()
    {
        $applicationRootDirectory = $this->getContainer()->getParameter('kernel.root_dir');
        $fileDirectory = realpath($applicationRootDirectory . '/../var/upload/images');
        $lastImage = $this->getLastImage();
        $filename = $fileDirectory . '/' . $lastImage->getName();
        unlink($filename);
    }

    private function givenExecutorNotEventCreator()
    {
        $this->setAuthToken(UserFixture::TEST_TOKEN_SECOND);
    }
}
