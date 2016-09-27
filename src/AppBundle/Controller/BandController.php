<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Infrastructure\AmbassadorController;
use AppBundle\Entity\Band;
use AppBundle\Entity\BandMember;
use AppBundle\Entity\Repository\BandRepository;
use AppBundle\Entity\User;
use AppBundle\Form\Ambassador\BandFormType;
use AppBundle\Form\Ambassador\BandMemberFormType;
use AppBundle\Response\ApiValidationError;
use AppBundle\Response\CreatedApiResponse;
use AppBundle\Response\EmptyApiResponse;
use AppBundle\Response\Infrastructure\AbstractApiResponse;
use AppBundle\Service\Ambassador\AmbassadorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Response\ApiError;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 * @Route("band")
 */
class BandController extends AmbassadorController
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
        return $this->listEntities($this->get('rockparade.band_repository'), $limit, $offset);
    }

    /**
     * View band by id
     * @Route("/{id}", name="band_view")
     * @Method("GET")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         200="Band was found",
     *         404="Band with given id was not found",
     *     }
     * )
     * @param string $id band name
     */
    public function viewAction(string $id): Response
    {
        return $this->viewEntity($this->get('rockparade.band_repository'), $id);
    }

    /**
     * Create new band
     * @Route("", name="band_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
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
     *             "requirement"="false",
     *             "description"="logins and short descriptions of band musicians"
     *         },
     *     },
     *     statusCodes={
     *         201="New band was created. Link to new resource provided in header 'Location'",
     *         400="Validation error",
     *     }
     * )
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createAndProcessForm($request, BandFormType::class);

        $apiResponseFactory = $this->get('rockparade.api_response_factory');
        $response = $apiResponseFactory->createResponse(
            $this->createApiOperation($request),
            $form,
            $this->getUser()
        );

        return $this->respond($response);
    }

    /**
     * Edit band
     * @Route("/{id}", name="band_edit")
     * @Method("PUT")
     * @Security("has_role('ROLE_USER')")
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
     *         404="Band with given id was not found",
     *     }
     * )
     * @param string $id band id
     */
    public function editAction(Request $request, string $id): Response
    {
        /** @var BandRepository $bandRepository */
        $bandRepository = $this->get('rockparade.band_repository');
        /** @var Band $band */
        $band = $bandRepository->findOneById($id);

        $form = $this->createForm(BandFormType::class);
        $this->processForm($request, $form);
        $form = $this->get('rockparade.band')->processFormAndUpdateBand($form, $band, $this->getUser());

        return $this->respond($this->createResponseFromUpdateForm($form));
    }

    /**
     * Add member to band
     * @Route("/members", name="band_member_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Band",
     *     requirements={
     *         {
     *             "name"="ambassador",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="band id"
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
     *         404="Band or User was not found",
     *     }
     * )
     */
    public function createMemberAction(Request $request): Response
    {
        $form = $this->createAndProcessForm($request, BandMemberFormType::class);

        $apiResponseFactory = $this->get('rockparade.api_response_factory');
        $response = $apiResponseFactory->createResponse(
            $this->createApiOperation($request),
            $form,
            $this->getUser()
        );

        return $this->respond($response);
    }

    /**
     * Delete member from band
     * @Route("/{id}/member/{userLogin}", name="band_member_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Band",
     *     statusCodes={
     *         204="Member was deleted from the band",
     *         404="Band or user was not found",
     *     }
     * )
     * @param string $id band id
     * @param string $userLogin member login
     */
    public function deleteMemberAction(string $id, string $userLogin): Response
    {
        return parent::deleteMember(new AmbassadorType(Band::class), $this->getUser(), $id, $userLogin);
    }
    
    /**
     * Update band member
     * @Route("/{id}/member", name="band_member_update")
     * @Method("PUT")
     * @Security("has_role('ROLE_USER')")
     * @ApiDoc(
     *     section="Band",
     *     requirements={
     *         {
     *             "name"="ambassador",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="band id"
     *         },
     *         {
     *             "name"="login",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="login of musician"
     *         },
     *         {
     *             "name"="short_description",
     *             "dataType"="string",
     *             "requirement"="true",
     *             "description"="short description of role in band"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="string",
     *             "requirement"="false",
     *             "description"="long description of musician"
     *         },
     *     },
     *     statusCodes={
     *         204="Band member was successfully updated",
     *         404="Band or user was not found",
     *     }
     * )
     * @param string $userLogin member login
     */
    public function updateMemberAction(Request $request)
    {
        $id = $request->get('ambassador');

        $bandRepository = $this->get('rockparade.band_repository');
        $band = $bandRepository->findOneById($id);

        if ($band) {
            $userLogin = $request->get('login');
            $userRepository = $this->get('rockparade.user_repository');
            $user = $userRepository->findOneByLogin($userLogin);

            if ($user) {
                $bandMemberRepository = $this->get('rockparade.band_member_repository');
                $bandMember = $bandMemberRepository->findByAmbassadorAndUser($band, $user);
                
                if ($bandMember) {
                    $form = $this->createForm(BandMemberFormType::class);
                    $this->processForm($request, $form);
                    $form = $this->get('rockparade.band')->processFormAndUpdateBandMember($form, $bandMember);
                    
                    $bandRepository->flush();

                    $response = $this->createResponseFromUpdateForm($form);
                } else {
                    $response = $this->createEntityNotFoundResponse(BandMember::class, $userLogin);
                }
            } else {
                $response = $this->createEntityNotFoundResponse(User::class, $userLogin);
            }
        } else {
            $response = $this->createEntityNotFoundResponse(Band::class, $id);
        }

        return $this->respond($response);
    }

    /**
     * @return ApiError|CreatedApiResponse|EmptyApiResponse
     */
    private function createResponseFromUpdateForm(FormInterface $form): AbstractApiResponse
    {
        if ($form->isValid()) {
            return new EmptyApiResponse(Response::HTTP_NO_CONTENT);
        } else {
            return new ApiValidationError($form);
        }
    }
}
