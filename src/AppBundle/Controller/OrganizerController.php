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
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     * @param int $limit Limit results. Default is 50
     * @param int $offset Starting serial number of result collection. Default is 0
     */
    public function listAction($limit = null, $offset = null): Response
    {
        return $this->respond(
            $this->createCompleteCollectionResponse($this->get('rockparade.organizer_repository'), $limit, $offset)
        );
    }
}
