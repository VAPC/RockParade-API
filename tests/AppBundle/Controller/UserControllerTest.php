<?php

namespace AppBundle\Controller;

use AppBundle\Fixture\UsersFixture;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vehsamrak
 */
class UserControllerTest extends WebTestCase
{

    const USER_NAME_NEW = 'Unit Tester';
    const USER_DESCRIPTION_NEW = 'Unit tester description';
    const USER_LOGIN_NEW = 'unittester';
    const USER_LOGIN_FIRST = 'first';
    const USER_LOGIN_SECOND = 'second';
    const USER_NAME_FIRST = 'Mr. First';
    const USER_NAME_SECOND = 'Mr. Second';

    /** @var Client */
    private $httpClient;

    /** {@inheritDoc} */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->loadFixtures(
            [
                UsersFixture::class,
            ]
        );
    }

    /** {@inheritDoc} */
    protected function setUp()
    {
        $this->httpClient = $this->makeClient();
    }

    /** {@inheritDoc} */
    protected function tearDown()
    {
        $this->httpClient = null;
    }

    /** @test */
    public function indexAction_GETUsersRequest_listOfAllUsers()
    {
        $this->httpClient->followRedirects();

        $contents = $this->sendGetRequestAndHandleResponse('/users');
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_FIRST, $contents['data'][0]['login']);
        $this->assertEquals(self::USER_NAME_FIRST, $contents['data'][0]['name']);
        $this->assertEquals(self::USER_LOGIN_SECOND, $contents['data'][1]['login']);
        $this->assertEquals(self::USER_NAME_SECOND, $contents['data'][1]['name']);
    }

    /** @test */
    public function viewAction_GETUsersViewLoginRequest_singleUserInfo()
    {
        $contents = $this->sendGetRequestAndHandleResponse('/users/view/first');
        $responseCode = $this->getResponseCode();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_FIRST, $contents['data']['login']);
        $this->assertEquals(self::USER_NAME_FIRST, $contents['data']['name']);
    }

    /** @test */
    public function viewAction_GETUsersViewNotExistingLoginRequest_userNotFoundError()
    {
        $contents = $this->sendGetRequestAndHandleResponse('/users/view/notexistinguser');
        $responseCode = $this->getResponseCode();

        $this->assertEquals(404, $responseCode);
        $this->assertEquals('User with login "notexistinguser" was not found.', $contents['error']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithLoginAndNameAndDescription_newUserCreated()
    {
        $parameters = $this->createParametersForNewUser();

        $this->sendPostRequest('/users/create', $parameters);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(200, $responseCode);
        $this->assertEquals(self::USER_LOGIN_NEW, $contents['data']['login']);
        $this->assertEquals(self::USER_NAME_NEW, $contents['data']['name']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithLoginAndNameOfExistingUser_userAlreadyExistsError()
    {
        $parameters = $this->createParametersForExistingUser();

        $this->sendPostRequest('/users/create', $parameters);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(409, $responseCode);
        $this->assertEquals('User with login or username "first" already exists.', $contents['error']);
    }

    /** @test */
    public function createAction_POSTUsersCreateWithEmptyParameters_missingParametersError()
    {
        $this->sendPostRequest('/users/create', []);
        $responseCode = $this->getResponseCode();
        $contents = $this->getResponseContents();

        $this->assertEquals(400, $responseCode);
        $this->assertEquals('Properties "login" and "name" are mandatory.', $contents['error']);
    }

    /**
     * @param string $route
     * @return array
     */
    protected function sendGetRequestAndHandleResponse(string $route): array
    {
        $this->httpClient->request(Request::METHOD_GET, $route);
        $response = $this->httpClient->getResponse();

        return json_decode($response->getContent(), true);
    }

    /**
     * @param string $route
     * @param array $parameters
     */
    protected function sendPostRequest(string $route, array $parameters = [])
    {
        $this->httpClient->request(Request::METHOD_POST, $route, $parameters);
    }

    private function getResponseContents()
    {
        return json_decode($this->httpClient->getResponse()->getContent(), true);
    }

    /**
     * @return int
     */
    private function getResponseCode()
    {
        $responseCode = $this->httpClient->getResponse()->getStatusCode();

        return $responseCode;
    }

    /**
     * @return array
     */
    private function createParametersForNewUser(): array
    {
        return [
            'login'       => self::USER_LOGIN_NEW,
            'name'        => self::USER_NAME_NEW,
            'description' => self::USER_DESCRIPTION_NEW,
        ];
    }

    /**
     * @return array
     */
    private function createParametersForExistingUser(): array
    {
        return [
            'login'       => self::USER_LOGIN_FIRST,
            'name'        => self::USER_NAME_NEW,
        ];
    }
}
