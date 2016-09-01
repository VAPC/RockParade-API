<?php

namespace AppBundle\Service\Vkontakte;

use GuzzleHttp\Client;

/**
 * Vkontakte API facade
 * @link https://vk.com/dev/methods
 * @author Vehsamrak
 */
class VkontakteClient
{

    const API_URL = 'https://api.vk.com/method/';
    const VK_REQUEST_TOKEN_URL = 'https://oauth.vk.com/access_token';

    /** @var float */
    private $version;

    /** @var string */
    private $vkClientId;

    /** @var string */
    private $vkSecret;

    public function __construct(float $version, string $vkClientId, string $vkSecret)
    {
        $this->version = $version;
        $this->vkClientId = $vkClientId;
        $this->vkSecret = $vkSecret;
    }

    public function getTokenByCode(string $vkAuthorizationCode, string $callbackUrl): AccessToken
    {
        $parameters = [
            'client_id'     => $this->vkClientId,
            'client_secret' => $this->vkSecret,
            'redirect_uri'  => $callbackUrl,
            'code'          => $vkAuthorizationCode,
        ];

        $vkontakteRequestTokenUrl = sprintf(
            '%s?%s',
            self::VK_REQUEST_TOKEN_URL,
            http_build_query($parameters)
        );

        $client = new Client();
        $httpResponse = $client->request('GET', $vkontakteRequestTokenUrl);
        $result = json_decode($httpResponse->getBody(), true);

        $userVkId = $result['user_id'];
        $vkToken = $result['access_token'];
        $userEmail = $result['email'] ?? '';

        return new AccessToken($userVkId, $vkToken, $userEmail);
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
