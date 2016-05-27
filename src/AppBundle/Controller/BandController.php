<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\Band;
use AppBundle\Response\ApiResnonse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("band")
 * @author Vehsamrak
 */
class BandController extends RestController
{
    /**
     * List all registered bands
     * @Route("/", name="bands_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         200="OK",
     *     }
     * )
     * @return Response
     */
    public function listAction(): Response
    {
        $bandRepository = $this->getDoctrine()->getRepository(Band::class);
        $response = new ApiResnonse($bandRepository->findAll(), Response::HTTP_OK);

        return $this->respond($response);
    }
}
