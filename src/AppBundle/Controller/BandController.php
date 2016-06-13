<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\CreateBand;
use AppBundle\Entity\Repository\BandRepository;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Response\CreatedApiResponse;
use AppBundle\Response\EmptyApiResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Entity\Band;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResponse;
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
     * View band by name
     * @Route("/{name}", name="band_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         200="Band was found",
     *         404="Band with given name was not found",
     *     }
     * )
     * @param string $name band name
     */
    public function viewAction(string $name): Response
    {
        /** @var BandRepository $bandRepository */
        $bandRepository = $this->getDoctrine()->getRepository(Band::class);
        $user = $bandRepository->findOneByName($name);

        if ($user) {
            $response = new ApiResponse($user, Response::HTTP_OK);
        } else {
            $response = $this->createBandNotFoundErrorResult($name);
        }

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
     *         201="New band was created. Link to new resource in header 'Location'",
     *         400="Validation error",
     *     }
     * )
     */
    public function createAction(Request $request): Response
    {
        return $this->respond($this->createBand($request));
    }

    /**
     * Edit band
     * @Route("/{name}", name="band_edit")
     * @Method("PUT")
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
     *         204="Band was edited with new data",
     *         400="Validation error",
     *     }
     * )
     * @param string $name band name
     */
    public function editAction(Request $request): Response
    {
        return $this->respond($this->updateBand($request));
    }

    private function createFormBandCreate(): Form
    {
        $formBuilder = $this->createFormBuilder(new CreateBand());
        $formBuilder->add('name', TextType::class);
        $formBuilder->add('users', TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    private function createAndPersistOrUpdateBandUsingForm(FormInterface $form, ObjectManager $objectManager)
    {
        if (!$form->isValid()) {
            return null;
        }

        $name = $form->get('name')->getData();
        $description = $form->get('description')->getData();
        $usersData = $form->get('users')->getData();

        if (!$usersData) {
            $form->addError(new FormError('Parameter "users" is mandatory'));
        } else {
            /** @var BandRepository $bandRepository */
            $bandRepository = $objectManager->getRepository(Band::class);
            $existingBandWithReplacableName = $bandRepository->findOneByName($name);

            if ($existingBandWithReplacableName) {
            	$form->addError(new FormError(sprintf('Band with name "%s" already exists.', $name)));
            }

            /** @var UserRepository $usersRepository */
            $usersRepository = $objectManager->getRepository(User::class);
            $users = array_map(
                function (string $userLogin) use ($usersRepository, $form) {
                    $user = $usersRepository->findOneByLogin($userLogin);

                    if (!$user) {
                        $form->addError(new FormError(sprintf('User "%s" was not found.', $userLogin)));
                    }

                    return $user;
                },
                $usersData
            );

            $band = new Band($name, $users, $description);
            $objectManager->persist($band);
        }
    }

    private function getLocationFromForm(FormInterface $form)
    {
        $bandName = $form->get('name')->getData();

        return $this->generateUrl('band_view', ['name' => $bandName]);
    }

    private function createBandNotFoundErrorResult(string $bandName): ApiError
    {
        return new ApiError(
            sprintf('Band with name "%s" was not found.', $bandName),
            Response::HTTP_NOT_FOUND
        );
    }

    private function updateBand(Request $request, bool $isNew = false): AbstractApiResponse
    {
        $form = $this->createFormBandCreate();
        $this->processForm($request, $form);

        $objectManager = $this->getDoctrine()->getManager();
        $this->createAndPersistOrUpdateBandUsingForm($form, $objectManager);

        if ($form->isValid()) {
            $objectManager->flush();
            $bandLocation = $this->getLocationFromForm($form);

            if ($isNew) {
                $response = new CreatedApiResponse($bandLocation);
            } else {
                $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
            }

            return $response;
        } else {
            $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);

            return $response;
        }
    }

    private function createBand(Request $request): AbstractApiResponse
    {
        return $this->updateBand($request, true);
    }
}
