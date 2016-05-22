<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Controller\Infrastructure\RestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Roles
 * @Route("roles")
 */
class RoleController extends RestController
{

    /**
     * @Route("/", name="roles_index")
     * @Method("GET")
     * @return Response
     */
    public function indexAction(): Response
    {
        $rolesRepository = $this->getDoctrine()->getRepository(Role::class);
        $roles = $rolesRepository->findAll();

        return $this->respond($roles);
    }
}
