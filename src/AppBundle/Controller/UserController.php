<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Actions with users
 * @author Vehsamrak
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
     * @param string $bandName user login
     */
    public function viewAction(string $login): Response
    {
        return $this->viewEntity($this->get('rockparade.user_repository'), $login);
    }

    /**
     * View current user
     * @Route("", name="user_view_current")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="User",
     *     statusCodes={
     *         200="Current user is logged in",
     *         401="Authentication required",
     *     }
     * )
     */
    public function viewCurrentAction(): Response
    {
        $user = $this->getUser();

        if ($user) {
            $response = new ApiResponse($user, Response::HTTP_OK);
        } else {
            $response = $this->createUserNotLoggedInErrorResult();
        }

        return $this->respond($response);
    }

    private function createUserNotLoggedInErrorResult(): ApiError
    {
        return new ApiError('You are not logged in.', Response::HTTP_UNAUTHORIZED);
    }
}
