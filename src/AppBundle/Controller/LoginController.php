<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Response\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("login")
 * @author Vehsamrak
 */
class LoginController extends RestController
{

    const VK_AUTHORIZATION_URL = 'https://oauth.vk.com/authorize';
    const VK_REQUEST_TOKEN_URL = 'https://oauth.vk.com/access_token';
    const VK_DISPLAY_POPUP = 'popup';
    const VK_DISPLAY_PAGE = 'page';
    const VK_RESPONSE_TYPE_CODE = 'code';

    /**
     * @Route("/vk", name="login_vk")
     */
    public function vkAction()
    {
        $parameters = [
            'client_id'     => $this->getParameter('vkontakte.client_id'),
            'redirect_uri'  => $this->generateVkCallbackUrl(),
            'display'       => self::VK_DISPLAY_PAGE,
            'response_type' => self::VK_RESPONSE_TYPE_CODE,
            'v'             => $this->getParameter('vkontakte.version'),
            'scope'         => $this->getPermissionMask(),
            'state'         => '', // string for state transfer. Can have any value
        ];

        $vkontakteAuthorizationUrl = sprintf(
            '%s?%s',
            self::VK_AUTHORIZATION_URL,
            http_build_query($parameters)
        );

        return $this->redirect($vkontakteAuthorizationUrl);
    }

    /**
     * @Route("/vk/callback", name="login_vk_oauth_callback")
     */
    public function vkOAuthCallbackAction(Request $request)
    {
        $vkAuthorizationCode = $request->get('code');

        $response = new ApiResponse(
            [
                'token' => $this->requestTokenByCode($vkAuthorizationCode),
            ],
            Response::HTTP_OK
        );

        return $this->respond($response);
    }

    /**
     * Vkontakte permission flags
     * @link https://vk.com/dev/permissions
     */
    private function getPermissionMask(): string
    {
        return join(
            ',',
            [
                'nohttps',
                'email',
                'offline',
            ]
        );
    }

    private function requestTokenByCode(string $vkAuthorizationCode): array
    {
        $parameters = [
            'client_id'     => $this->getParameter('vkontakte.client_id'),
            'client_secret' => $this->getParameter('vkontakte.client_secret'),
            'redirect_uri'  => $this->generateVkCallbackUrl(),
            'code'          => $vkAuthorizationCode,
        ];

        $vkontakteRequestTokenUrl = sprintf(
            '%s?%s',
            self::VK_REQUEST_TOKEN_URL,
            http_build_query($parameters)
        );

        return json_decode(file_get_contents($vkontakteRequestTokenUrl), true);
    }

    private function generateVkCallbackUrl(): string
    {
        return $this->generateUrl('login_vk_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
