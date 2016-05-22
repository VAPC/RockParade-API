<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Controller\Infrastructure\RestController;
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
}
