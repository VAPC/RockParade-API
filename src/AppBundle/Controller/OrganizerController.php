<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 * @Route("organizer")
 */
class OrganizerController extends RestController
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
     * @Route("/{organizerName}", name="organizer_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="Organizer",
     *     requirements={
     *         {
     *             "name"="organizerName",
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
     * @param string $organizerName organizer name
     */
    public function viewAction(string $organizerName): Response
    {
        return $this->viewEntity($this->get('rockparade.organizer_repository'), $organizerName);
    }
}
