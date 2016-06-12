<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\CreateBand;
use AppBundle\Entity\User;
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
use Symfony\Component\Form\FormError;
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
     *         201="New band was created",
     *         400="Validation error",
     *     }
     * )
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createFormBandCreate();
        $this->processForm($request, $form);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $band = $this->createBandUsingForm($form, $entityManager);

            if ($band) {
                $entityManager->persist($band);
            }
        }

        if ($form->isValid()) {
            $entityManager->flush();

            $response = new EmptyApiResponse(Response::HTTP_CREATED);
        } else {
            $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
        }

        return $this->respond($response);
    }

    private function createFormBandCreate(): Form
    {
        $formBuilder = $this->createFormBuilder(new CreateBand());
        $formBuilder->add('name', TextType::class);
        $formBuilder->add('users', TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    private function createBandUsingForm(FormInterface $form, EntityManager $entityManager): Band
    {
        $name = $form->get('name')->getData();
        $description = $form->get('description')->getData();
        $usersData = $form->get('users')->getData();

        if (!$usersData) {
        	$form->addError(new FormError('Parameter \'users\' is mandatory'));
        } else {
            $usersRepository = $entityManager->getRepository(User::class);
            $users = array_map(
                function (string $userLogin) use ($usersRepository, $form) {
                    $user = $usersRepository->findOneByLogin($userLogin);

                    if (!$user) {
                        $form->addError(new FormError(sprintf('User %s was not found.', $userLogin)));
                    }

                    return $user;
                },
                $usersData
            );

            return new Band($name, $users, $description);
        }

        return null;
    }
}
