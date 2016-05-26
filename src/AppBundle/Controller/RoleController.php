<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\RoleRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRepository;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResnonse;
use AppBundle\Response\EmptyApiResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
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

        foreach ($roles as $key => $role) {
            $roles[$role->getName()] = $role;
            unset($roles[$key]);
        }

        $response = new ApiResnonse($roles, Response::HTTP_OK);

        return $this->respond($response);
    }

    /**
     * Assign roles to user
     * @Route("/assign", name="roles_assign")
     * @Method("POST")
     * @ApiDoc(
     *     section="Roles",
     *     requirements={
     *         {
     *             "name"="login",
     *             "dataType"="string",
     *             "requirement"="\w",
     *             "description"="user login"
     *         },
     *         {
     *             "name"="roles",
     *             "dataType"="array",
     *             "requirement"="\w",
     *             "description"="applicable roles"
     *         }
     *     },
     *     statusCodes={
     *         200="Roles were assigned to user",
     *         400="Mandatory parameters are missed or not all provided roles are valid",
     *         404="User with provided login was not found",
     *     }
     * )
     * @param Request $request
     * @return Response
     */
    public function assignAction(Request $request): Response
    {
        $userLogin = filter_var($request->request->get('login'), FILTER_SANITIZE_STRING) ?: '';
        $roleNames = (array) $request->request->get('roles');

        if ($userLogin && $roleNames) {
            $doctrineService = $this->getDoctrine();
            /** @var UserRepository $userRepository */
            $userRepository = $doctrineService->getRepository(User::class);
            $user = $userRepository->findOneByLogin($userLogin);

            if ($user) {
                /** @var RoleRepository $roleRepository */
                $roleRepository = $doctrineService->getRepository(Role::class);
                $roles = $roleRepository->findByNames($roleNames);
                
                if (count($roleNames) === count($roles)) {
                    foreach ($roles as $role) {
                        $user->addRole($role);
                    }
                    
                    $roleRepository->flush();
                    $response = new EmptyApiResponse(Response::HTTP_OK);
                } else {
                    $response = new ApiError('Not all provided roles are valid.', Response::HTTP_BAD_REQUEST);
                }
            } else {
                $response = new ApiError('User with provided login was not found.', Response::HTTP_NOT_FOUND);
            }
        } else {
            $response = new ApiError('Properties "login" and "roles" are mandatory.', Response::HTTP_BAD_REQUEST);
        }

        return $this->respond($response);
    }
}
