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
     * List all registered users
     * @Route("s/", name="users_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="User",
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     */
    public function listAction(): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $allUsers = $userRepository->findAll();

        $response = new ApiResponse($allUsers, Response::HTTP_OK);

        return $this->respond($response);
    }

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
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneByLogin($login);

        if ($user) {
            $response = new ApiResponse($user, Response::HTTP_OK);
        } else {
            $response = $this->createUserNotFoundErrorResult($login);
        }

        return $this->respond($response);
    }

    /**
     * Create new user with given login, name and description
     * @Route("/create", name="users_create")
     * @Method("POST")
     * @ApiDoc(
     *     section="User",
     *     requirements={
     *         {
     *             "name"="login",
     *             "dataType"="string",
     *             "requirement"="\w",
     *             "description"="user login in single word"
     *         },
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="[\w\s]",
     *             "description"="user full name"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"=".",
     *             "description"="user description"
     *         },
     *     },
     *     statusCodes={
     *         201="New user was created",
     *         400="Mandatory parameters are missed",
     *         409="User with given login or username already exists",
     *     }
     * )
     */
    public function createAction(Request $request): Response
    {
        $userLogin = (string) $request->request->getAlnum('login');
        $userName = $request->request->get('name');
        $description = $request->request->get('description');

        if (empty($userLogin) || empty($userName)) {
            return $this->respond(
                new ApiError('Properties "login" and "name" are mandatory.', Response::HTTP_BAD_REQUEST)
            );
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        if ($userRepository->findOneByLogin($userLogin)) {
            $result = $this->createUserExistsErrorResult($userLogin);
        } elseif ($userRepository->findOneByName($userName)) {
            $result = $this->createUserExistsErrorResult($userName);
        } else {
            $user = new User($userLogin, $userName, $description);
            $userRepository->persist($user);
            $userRepository->flush();

            $result = new ApiResponse($user, Response::HTTP_CREATED);
        }

        return $this->respond($result);
    }

    private function createUserExistsErrorResult(string $userLoginOrName): ApiError
    {
        return new ApiError(
            sprintf(
                'User with login or username "%s" already exists.',
                $userLoginOrName
            ), Response::HTTP_CONFLICT
        );
    }

    private function createUserNotFoundErrorResult(string $userLogin): ApiError
    {
        return new ApiError(
            sprintf('User with login "%s" was not found.', $userLogin),
            Response::HTTP_NOT_FOUND
        );
    }
}
