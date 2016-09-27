<?php

namespace AppBundle\Controller\Infrastructure;

use AppBundle\Entity\Infrasctucture\Ambassador;
use AppBundle\Entity\Infrasctucture\AmbassadorMemberRepository;
use AppBundle\Entity\Infrasctucture\AmbassadorRepository;
use AppBundle\Entity\User;
use AppBundle\Response\ApiError;
use AppBundle\Response\EmptyApiResponse;
use AppBundle\Service\Ambassador\AmbassadorType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vehsamrak
 */
class AmbassadorController extends RestController
{

    protected function deleteMember(
        AmbassadorType $ambassadorType,
        User $executor,
        string $id,
        string $userLogin
    ): Response
    {
        $entityName = $ambassadorType->getValue();
        /** @var AmbassadorRepository $repository */
        $repository = $this->getDoctrine()->getRepository($entityName);
        /** @var Ambassador $ambassador */
        $ambassador = $repository->findOneById($id);

        if ($ambassador) {
            if ($ambassador->getCreator()->getLogin() !== $executor->getLogin()) {
                $response = new ApiError('Only ambassador creator can delete it\'s members.', Response::HTTP_FORBIDDEN);
            } else {
                $userRepository = $this->get('rockparade.user_repository');
                $user = $userRepository->findOneByLogin($userLogin);

                if ($user) {
                    $memberClass = $ambassador->getMemberClass();
                    /** @var AmbassadorMemberRepository $ambassadorMemberRepository */
                    $ambassadorMemberRepository = $this->getDoctrine()->getRepository($memberClass);
                    $ambassadorMember = $ambassadorMemberRepository->findByAmbassadorAndUser($ambassador, $user);

                    if ($ambassadorMember) {
                        $ambassador->removeMember($ambassadorMember);
                        $repository->flush();

                        $response = new EmptyApiResponse(Response::HTTP_NO_CONTENT);
                    } else {
                        $response = $this->createEntityNotFoundResponse($memberClass, $userLogin);
                    }
                } else {
                    $response = $this->createEntityNotFoundResponse(User::class, $userLogin);
                }
            }
        } else {
            $response = $this->createEntityNotFoundResponse($entityName, $id);
        }

        return $this->respond($response);
    }
}
