<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\BandMemberDTO;
use AppBundle\Entity\DTO\CreateBand;
use AppBundle\Entity\Repository\BandRepository;
use AppBundle\Response\CreatedApiResponse;
use AppBundle\Response\EmptyApiResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Service\BandService;
use Symfony\Component\Form\Form;
use AppBundle\Controller\Infrastructure\RestController;
use AppBundle\Response\ApiError;
use AppBundle\Response\ApiResponse;
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
     */
    public function listAction(): Response
    {
        $bandRepository = $this->get('rockparade.band_repository');
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
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($name);

        if ($band) {
            $response = new ApiResponse($band, Response::HTTP_OK);
        } else {
            $response = $this->createBandNotFoundErrorResult($name);
        }

        return $this->respond($response);
    }

    /**
     * Create new band
     * @Route("", name="band_create")
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
     *             "description"="logins and short descriptions of band musicians"
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
        $form = $this->createFormBandCreate();
        $this->processForm($request, $form);
        $form = $this->get('rockparade.band')->processFormAndCreateBand($form);

        return $this->respond($this->createResponseFromCreateForm($form));
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
    public function editAction(Request $request, string $name): Response
    {
        /** @var BandRepository $bandRepository */
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($name);

        $form = $this->createFormBandCreate();
        $this->processForm($request, $form);
        $form = $this->get('rockparade.band')->processFormAndUpdateBand($form, $band);

        return $this->respond($this->createResponseFromUpdateForm($form));
    }

    /**
     * List all band members
     * @Route("/{name}/members", name="band_members")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         200="OK",
     *         404="Band was not found",
     *     }
     * )
     */
    public function listMembersAction(string $name): Response
    {
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($name);

        if (!$band) {
            $response = $this->createBandNotFoundErrorResult($name);
        } else {
            $response = new ApiResponse($band->getMembers(), Response::HTTP_OK);
        }

        return $this->respond($response);
    }

    /**
     * Add member to band
     * @Route("/{name}/members", name="band_member_add")
     * @Method("POST")
     * @ApiDoc(
     *     section="Band",
     *     requirements={
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
     *             "description"="short description of musicians role in band"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"="false",
     *             "description"="long description of musician"
     *         },
     *     },
     *     statusCodes={
     *         200="Member was added to the band",
     *         400="Validation error",
     *     }
     * )
     * @param string $name band name
     */
    public function addMemberAction(Request $request, string $name): Response
    {
        $form = $this->createFormBandMember();
        $this->processForm($request, $form);

        if ($form->isValid()) {
            $bandRepository = $this->get('rockparade.band_repository');
            $band = $bandRepository->findOneByName($name);

            if (!$band) {
                $response = $this->createBandNotFoundErrorResult($name);
            } else {
                $newUserLogin = $form->get('login')->getData();
                $newUser = $this->get('rockparade.user_repository')->findOneByLogin($newUserLogin);

                if (!$newUser) {
                    $response = $this->createUserNotFoundErrorResult($newUserLogin);
                } else {
                    $bandMemberRepository = $this->get('rockparade.band_member_repository');
                    $shortDescription = (string) $form->get('short_description')->getData();
                    $description = (string) $form->get('description')->getData();
                    $bandMember = $bandMemberRepository->getOrCreateByBandAndUser(
                        $band,
                        $newUser,
                        $shortDescription,
                        $description
                    );

                    $band->addMember($bandMember);
                    $bandRepository->flush();

                    $response = new EmptyApiResponse(Response::HTTP_OK);
                }
            }
        } else {
            $response = new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
        }

        return $this->respond($response);
    }

    /**
     * Delete member from band
     * @Route("/{name}/member/{userLogin}", name="band_member_delete")
     * @Method("DELETE")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         204="Member was deleted from the band",
     *         404="Band or user was not found",
     *     }
     * )
     * @param string $name band name
     * @param string $userLogin member login
     */
    public function deleteMemberAction(string $name, string $userLogin)
    {
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($name);

        if ($band) {
            $userRepository = $this->get('rockparade.user_repository');
            $user = $userRepository->findOneByLogin($userLogin);

            if ($user) {
                $bandMemberRepository = $this->get('rockparade.band_member_repository');
                $bandMember = $bandMemberRepository->findByBandAndUser($band, $user);
                $band->removeMember($bandMember);
                $bandRepository->flush();

                $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
            } else {
                $response = $this->createUserNotFoundErrorResult($userLogin);
            }
        } else {
            $response = $this->createBandNotFoundErrorResult($name);
        }

        return $this->respond($response);
    }

    private function createFormBandMember(): Form
    {
        $formBuilder = $this->createFormBuilder(new BandMemberDTO());
        $formBuilder->add('login', TextType::class);
        $formBuilder->add('short_description', TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    private function createBandNotFoundErrorResult(string $bandName): ApiError
    {
        return new ApiError(
            sprintf('Band with name "%s" was not found.', $bandName),
            Response::HTTP_NOT_FOUND
        );
    }

    private function createUserNotFoundErrorResult(string $userLogin): ApiError
    {
        return new ApiError(
            sprintf('User with login "%s" was not found.', $userLogin),
            Response::HTTP_NOT_FOUND
        );
    }

    private function createFormBandCreate(): Form
    {
        $formBuilder = $this->createFormBuilder(new CreateBand());
        $formBuilder->add('name', TextType::class);
        $formBuilder->add(BandService::ATTRIBUTE_MEMBERS, TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    private function getLocationFromForm(FormInterface $form): string
    {
        $bandName = $form->get('name')->getData();

        return $this->generateUrl('band_view', ['name' => $bandName]);
    }

    /**
     * @param $form
     * @param $band
     * @return ApiError|CreatedApiResponse|EmptyApiResponse
     */
    private function createResponseFromCreateForm(FormInterface $form): AbstractApiResponse
    {
        if ($form->isValid()) {
            return new CreatedApiResponse($this->getLocationFromForm($form));
        } else {
            return new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return ApiError|CreatedApiResponse|EmptyApiResponse
     */
    private function createResponseFromUpdateForm(FormInterface $form): AbstractApiResponse
    {
        if ($form->isValid()) {
            return new EmptyApiResponse(Response::HTTP_NO_CONTENT);
        } else {
            return new ApiError($this->getFormErrors($form), Response::HTTP_BAD_REQUEST);
        }
    }
}
