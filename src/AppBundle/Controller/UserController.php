<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
}
