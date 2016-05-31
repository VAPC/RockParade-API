<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\CreateBand;
use AppBundle\Entity\User;
use AppBundle\Exception\UserNotFound;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\Band;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResponse;
use AppBundle\Response\EmptyApiResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("band")
 * @author Vehsamrak
 */
class BandController extends RestController
{
    /**
     * List all registered bands
     * @Route("s/", name="bands_list")
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
        $response = new ApiResponse($bandRepository->findAll(), Response::HTTP_OK);

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
     *             "requirement"="true",
     *             "description"="logins of band musicians"
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
            $entityManager = $this->getDoctrine()->getManager();

            $band = $this->createBandUsingForm($form, $entityManager);

            $entityManager->persist($band);
            $entityManager->flush();

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
        $formBuilder = $this->createFormBuilder(new CreateBand());
        $formBuilder->add('name', TextType::class);
        $formBuilder->add('users', TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    /**
     * @param FormInterface $form
     * @param EntityManager $entityManager
     * @return Band
     */
    private function createBandUsingForm(FormInterface $form, EntityManager $entityManager): Band
    {
        $name = $form->get('name')->getData();
        $description = $form->get('description')->getData();

        $usersRepository = $entityManager->getRepository(User::class);
        $users = array_map(
            function (string $userLogin) use ($usersRepository) {
                $user = $usersRepository->findOneByLogin($userLogin);

                if (!$user) {
                    throw new UserNotFound($userLogin);
                }

                return $user;
            },
            $form->get('users')->getData()
        );

        return new Band($name, $users, $description);
    }
}
