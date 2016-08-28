<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\CreateBandMemberDTO;
use AppBundle\Entity\DTO\CreateBand;
use AppBundle\Entity\DTO\UpdateBandMemberDTO;
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
     * @Route("s/{limit}/{offset}", name="bands_list")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
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
            $this->createCollectionResponse(
                $this->get('rockparade.band_repository'),
                $limit,
                $offset
            )
        );
    }

    /**
     * View band by name
     * @Route("/{bandName}", name="band_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         200="Band was found",
     *         404="Band with given name was not found",
     *     }
     * )
     * @param string $bandName band name
     */
    public function viewAction(string $bandName): Response
    {
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($bandName);

        if ($band) {
            $response = new ApiResponse($band, Response::HTTP_OK);
        } else {
            $response = $this->createBandNotFoundErrorResult($bandName);
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
     *             "name"="members",
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
     * @Route("/{bandName}", name="band_edit")
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
     * @param string $bandName band name
     */
    public function editAction(Request $request, string $bandName): Response
    {
        /** @var BandRepository $bandRepository */
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($bandName);

        $form = $this->createFormBandCreate();
        $this->processForm($request, $form);
        $form = $this->get('rockparade.band')->processFormAndUpdateBand($form, $band);

        return $this->respond($this->createResponseFromUpdateForm($form));
    }

    /**
     * List all band members
     * @Route("/{bandName}/members", name="band_members")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         200="OK",
     *         404="Band was not found",
     *     }
     * )
     */
    public function listMembersAction(string $bandName): Response
    {
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($bandName);

        if (!$band) {
            $response = $this->createBandNotFoundErrorResult($bandName);
        } else {
            $response = new ApiResponse($band->getMembers(), Response::HTTP_OK);
        }

        return $this->respond($response);
    }

    /**
     * Add member to band
     * @Route("/{bandName}/members", name="band_member_add")
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
     * @param string $bandName band name
     */
    public function addMemberAction(Request $request, string $bandName): Response
    {
        $form = $this->createFormCreateBandMember();
        $this->processForm($request, $form);

        if ($form->isValid()) {
            $bandRepository = $this->get('rockparade.band_repository');
            $band = $bandRepository->findOneByName($bandName);

            if (!$band) {
                $response = $this->createBandNotFoundErrorResult($bandName);
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
     * @Route("/{bandName}/member/{userLogin}", name="band_member_delete")
     * @Method("DELETE")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         204="Member was deleted from the band",
     *         404="Band or user was not found",
     *     }
     * )
     * @param string $bandName band name
     * @param string $userLogin member login
     */
    public function deleteMemberAction(string $bandName, string $userLogin)
    {
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($bandName);

        if ($band) {
            $userRepository = $this->get('rockparade.user_repository');
            $user = $userRepository->findOneByLogin($userLogin);

            if ($user) {
                $bandMemberRepository = $this->get('rockparade.band_member_repository');
                $bandMember = $bandMemberRepository->findByBandAndUser($band, $user);

                if ($bandMember) {
                    $band->removeMember($bandMember);
                    $bandRepository->flush();

                    $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
                } else {
                    $response = $this->createBandMemberNotFoundErrorResult($userLogin, $bandName);
                }
            } else {
                $response = $this->createUserNotFoundErrorResult($userLogin);
            }
        } else {
            $response = $this->createBandNotFoundErrorResult($bandName);
        }

        return $this->respond($response);
    }
    
    /**
     * Update band member
     * @Route("/{bandName}/member/{userLogin}", name="band_member_update")
     * @Method("PUT")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         204="Band member was successfully updated",
     *         404="Band or user was not found",
     *     }
     * )
     * @param string $bandName band name
     * @param string $userLogin member login
     */
    public function updateMemberAction(Request $request, string $bandName, string $userLogin)
    {
        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneByName($bandName);

        if ($band) {
            $userRepository = $this->get('rockparade.user_repository');
            $user = $userRepository->findOneByLogin($userLogin);

            if ($user) {
                $bandMemberRepository = $this->get('rockparade.band_member_repository');
                $bandMember = $bandMemberRepository->findByBandAndUser($band, $user);
                
                if ($bandMember) {
                    $form = $this->createFormUpdateBandMember();
                    $this->processForm($request, $form);
                    $form = $this->get('rockparade.band')->processFormAndUpdateBandMember($form, $bandMember);
                    
                    $bandRepository->flush();

                    $response = $this->createResponseFromUpdateForm($form);
                } else {
                    $response = $this->createBandMemberNotFoundErrorResult($userLogin, $bandName);
                }

            } else {
                $response = $this->createUserNotFoundErrorResult($userLogin);
            }
        } else {
            $response = $this->createBandNotFoundErrorResult($bandName);
        }

        return $this->respond($response);
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

    private function createBandMemberNotFoundErrorResult(string $userLogin, string $bandName): ApiError
    {
        return new ApiError(
            sprintf('Band member with login "%s" was not found for band "%s".', $userLogin, $bandName),
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

    private function createLocationByNameFieldInForm(FormInterface $form): string
    {
        $bandName = $form->get('name')->getData();

        return $this->generateUrl('band_view', ['bandName' => $bandName]);
    }

    /**
     * @param $form
     * @param $band
     * @return ApiError|CreatedApiResponse|EmptyApiResponse
     */
    private function createResponseFromCreateForm(FormInterface $form): AbstractApiResponse
    {
        if ($form->isValid()) {
            return new CreatedApiResponse($this->createLocationByNameFieldInForm($form));
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

    private function createFormUpdateBandMember(): FormInterface
    {
        $formBuilder = $this->createFormBuilder(new UpdateBandMemberDTO());
        $formBuilder->add('short_description', TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }

    private function createFormCreateBandMember(): FormInterface
    {
        $formBuilder = $this->createFormBuilder(new CreateBandMemberDTO());
        $formBuilder->add('login', TextType::class);
        $formBuilder->add('short_description', TextType::class);
        $formBuilder->add('description', TextareaType::class);

        return $formBuilder->getForm();
    }
}
