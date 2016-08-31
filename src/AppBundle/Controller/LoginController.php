<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\User;
use AppBundle\Response\ApiResponse;
use AppBundle\Service\IdGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * Login with Vkontakte OAuth.
     * Client should follow received location to authorize on vkontakte site.
     * If success, "token" will be received.
     * @Route("/vk", name="login_vk")
     * @Method("GET")
     * @ApiDoc(
     *     section="Login",
     *     statusCodes={
     *         302="Redirect to vkontakte login page",
     *     }
     * )
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
     * @Method("GET")
     */
    public function vkOAuthCallbackAction(Request $request)
    {
        $vkAuthorizationCode = $request->get('code');

        try {
            $token = $this->requestVkTokenByCode($vkAuthorizationCode);
        } catch (ClientException $e) {
            return $this->redirectToRoute('login_vk');
        }

        $response = new ApiResponse(
            [
                'token' => $token,
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
                'email',
                'offline',
            ]
        );
    }

    private function requestVkTokenByCode(string $vkAuthorizationCode): string
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

        $client = new Client();
        $httpResponse = $client->request('GET', $vkontakteRequestTokenUrl);
        $result = json_decode($httpResponse->getBody(), true);

        $userVkId = $result['user_id'];
        $vkAccessToken = $result['access_token'];
        $userEmail = $result['email'] ?? null;

        $userRepository = $this->get('rockparade.user_repository');
        $user = $userRepository->findUserByVkId($userVkId);

        if ($user) {
            $user->setVkAccessToken($vkAccessToken);
        } else {
            $id = IdGenerator::generateId();
            $vkontakteClient = $this->get('rockparade.vkontakte');
            $name = $vkontakteClient->getUserName($vkAccessToken);

            $user = new User($id, $name, $userVkId, $vkAccessToken, $userEmail);

            $userRepository->persist($user);
        }

        $userRepository->flush();

        return $vkAccessToken;
    }

    private function generateVkCallbackUrl(): string
    {
        return $this->generateUrl('login_vk_oauth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
