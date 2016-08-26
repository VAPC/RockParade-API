<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Actions with users
 * @Route("user")
 */
class UserController extends RestController
{

    /**
     * View single user by login
     * @Route("/{login}", name="user_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="User",
     *     statusCodes={
     *         200="User was found",
     *         404="User with given login was not found",
     *     }
     * )
     * @param string $login user login
     */
    public function viewAction(string $login): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->get('rockparade.user_repository');
        $user = $userRepository->findOneByLogin($login);

        if ($user) {
            $response = new ApiResponse($user, Response::HTTP_OK);
        } else {
            $response = $this->createUserNotFoundErrorResult($login);
        }

        return $this->respond($response);
    }

    private function createUserNotFoundErrorResult(string $userLogin): ApiError
    {
        return new ApiError(
            sprintf('User with login "%s" was not found.', $userLogin),
            Response::HTTP_NOT_FOUND
        );
    }
}
