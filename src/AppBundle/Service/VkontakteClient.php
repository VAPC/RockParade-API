<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;

/**
 * Vkontakte API facade
 * @link https://vk.com/dev/methods
 * @author Vehsamrak
 */
class VkontakteClient
{

    const API_URL = 'https://api.vk.com/method/';
    /**
     * @var float
     */
    private $version;

    public function __construct(float $version)
    {
        $this->version = $version;
    }

    public function getUserName(string $token)
    {
        $parameters = [
            'fields' => join(',', ['nickname', 'screen_name']),
        ];

        $result = $this->getMethod('users.get', $token, $parameters);

        $nickname = $result['nickname'];
        $firstName = $result['first_name'];
        $lastName = $result['last_name'];

        $username = $nickname;

        if (!$username) {
            $username = trim(sprintf('%s %s', $firstName, $lastName));
        }

        if (!$username) {
            $username = $result['screen_name'];
        }

        return $username;
    }

    private function getMethod(string $method, string $token, array $parameters = []): array
    {
        $parameters = array_merge(
            $parameters,
            [
                'access_token' => $token,
                'v'            => $this->version,
            ]
        );

        $client = new Client();
        $httpResponse = $client->request(
            'GET',
            sprintf('%s%s?%s', self::API_URL, $method, http_build_query($parameters))
        );

        $decodedResponse = json_decode($httpResponse->getBody(), true)['response'];

        return reset($decodedResponse);
    }
}
