<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\UserRepository;
use AppBundle\Response\ApiError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Actions with users
 * @Route("users")
 */
class UserController extends RestController
{

    /**
     * @Route("/", name="users_index")
     * @Method("GET")
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $allUsers = $userRepository->findAll();

        return $this->respond($allUsers);
    }

    /**
     * @Route("/view/{userLogin}", name="users_view")
     * @Method("GET")
     * @param string $userLogin
     * @return Response
     */
    public function viewAction(string $userLogin): Response
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneByLogin($userLogin);

        return $this->respond($user);
    }

    /**
     * @Route("/create", name="users_add")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $userLogin = $request->request->getAlnum('login');
        $userName = $request->request->get('name');
        $description = $request->request->get('description');

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

            $result = $user;
        }

        return $this->respond($result);
    }

    /**
     * @param string $userLoginOrName
     * @return ApiError
     */
    private function createUserExistsErrorResult(string $userLoginOrName)
    {
        return new ApiError(
            sprintf(
                'User with login or username "%s" already exists.',
                $userLoginOrName
            ), Response::HTTP_CONFLICT
        );
    }
}
