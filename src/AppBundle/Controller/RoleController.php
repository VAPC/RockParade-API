<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Response\ApiResnonse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * List all available user roles
     * @Route("/", name="roles_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="Roles",
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     * @return Response
     */
    public function listAction(): Response
    {
        $rolesRepository = $this->getDoctrine()->getRepository(Role::class);
        $roles = $rolesRepository->findAll();

        $response = new ApiResnonse($roles, Response::HTTP_OK);

        return $this->respond($response);
    }
}
