<?php

namespace AppBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\Band;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResnonse;
use AppBundle\Response\EmptyApiResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * Create new band
     * @Route("/create", name="band_create")
     * @Method("POST")
     * @ApiDoc(
     *     section="Band",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="band name"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="band description"
     *         },
     *         {
     *             "name"="users",
     *             "dataType"="array",
     *             "requirement"="",
     *             "description"="band users"
     *         },
     *     },
     *     statusCodes={
     *         200="New band was created",
     *         400="Validation error",
     *     }
     * )
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createBandCreateForm();
        $this->processForm($request, $form);

        if ($form->isValid()) {
            $response = new EmptyApiResponse(Response::HTTP_OK);
        } else {
            $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
        }

        return $this->respond($response);
    }

    /**
     * @return Form
     */
    private function createBandCreateForm()
    {
        $formBuilder = $this->createFormBuilder(
            new class
            {
                /** @Assert\NotBlank(message="Parameter 'name' is mandatory") */
                public $name;

                /** @Assert\NotBlank(message="Parameter 'description' is mandatory") */
                public $description;
            }
        )
            ->add('name', TextType::class)
            ->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }
}
