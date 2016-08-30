<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("login")
 * @author Vehsamrak
 */
class LoginController extends Controller
{

    const VK_AUTHORIZATION_URL = 'https://oauth.vk.com/authorize';
    const VK_DISPLAY_POPUP = 'popup';
    const VK_RESPONSE_TYPE_CODE = 'code';

    /**
     * @Route("/vk", name="login_vk")
     */
    public function vkAction()
    {
        $parameters = [
            'client_id'     => $this->getParameter('vkontakte.client_id'),
            'redirect_uri'  => $this->generateUrl('login_vk_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'display'       => self::VK_DISPLAY_POPUP,
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
        var_dump($request);

        return new Response('Success!');
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
}
