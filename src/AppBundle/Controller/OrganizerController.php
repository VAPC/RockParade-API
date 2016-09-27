<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\AmbassadorController;
use AppBundle\Entity\Organizer;
use AppBundle\Form\Ambassador\OrganizerFormType;
use AppBundle\Form\Ambassador\OrganizerMemberFormType;
use AppBundle\Service\Ambassador\AmbassadorType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 * @Route("organizer")
 */
class OrganizerController extends AmbassadorController
{

    /**
     * List all registered organizers
     * @Route("s/{limit}/{offset}", name="organizers_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="Organizer",
     *     requirements={
     *         {
     *             "name"="limit",
     *             "dataType"="int",
     *             "requirement"="false",
     *             "description"="limit number. Default is 50"
     *         },
     *         {
     *             "name"="offset",
     *             "dataType"="int",
     *             "requirement"="false",
     *             "description"="offset number. Default is 0"
     *         },
     *     },
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     * @param int $limit Limit results. Default is 50
     * @param int $offset Starting serial number of result collection. Default is 0
     */
    public function listAction($limit = null, $offset = null): Response
    {
        return $this->listEntities($this->get('rockparade.organizer_repository'), $limit, $offset);
    }

    /**
     * View organizer by name
     * @Route("/{id}", name="organizer_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="Organizer",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="organizer name"
     *         },
     *     },
     *     statusCodes={
     *         200="Organizer was found",
     *         404="Organizer with given name was not found",
     *     }
     * )
     * @param string $id organizer id
     */
    public function viewAction(string $id): Response
    {
        return $this->viewEntity($this->get('rockparade.organizer_repository'), $id);
    }

    /**
     * Create new organizer
     * @Route("", name="organizer_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Organizer",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="organization name"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="organization description"
     *         },
     *         {
     *             "name"="members",
     *             "dataType"="array",
     *             "requirement"="false",
     *             "description"="logins and short descriptions of organization members"
     *         },
     *     },
     *     statusCodes={
     *         201="New organizer was created. Link to new resource provided in header 'Location'",
     *         400="Validation error",
     *     }
     * )
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createAndProcessForm($request, OrganizerFormType::class);

        $apiResponseFactory = $this->get('rockparade.api_response_factory');
        $response = $apiResponseFactory->createResponse(
            $this->createApiOperation($request),
            $form,
            $this->getUser()
        );

        return $this->respond($response);
    }

    /**
     * Add new member to organization
     * @Route("/members", name="organizer_member_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Organizer",
     *     requirements={
     *         {
     *             "name"="ambassador",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="organizer id"
     *         },
     *         {
     *             "name"="login",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="user login"
     *         },
     *         {
     *             "name"="short_description",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="short description of user role in organization"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"="false",
     *             "description"="long description of user"
     *         },
     *     },
     *     statusCodes={
     *         201="Member was added to organization",
     *         400="Validation error",
     *         404="Organizer or User was not found",
     *     }
     * )
     */
    public function createMemberAction(Request $request): Response
    {
        $form = $this->createAndProcessForm($request, OrganizerMemberFormType::class);

        $apiResponseFactory = $this->get('rockparade.api_response_factory');
        $response = $apiResponseFactory->createResponse(
            $this->createApiOperation($request),
            $form,
            $this->getUser()
        );

        return $this->respond($response);
    }

    /**
     * Delete member from organizer
     * @Route("/{id}/member/{userLogin}", name="organizer_member_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Organizer",
     *     statusCodes={
     *         204="Member was deleted from organizer",
     *         404="Organizer or user was not found",
     *     }
     * )
     * @param string $id band id
     * @param string $userLogin member login
     */
    public function deleteMemberAction(string $id, string $userLogin): Response
    {
        return parent::deleteMember(new AmbassadorType(Organizer::class), $this->getUser(), $id, $userLogin);
    }
}
