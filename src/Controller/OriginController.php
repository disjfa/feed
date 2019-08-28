<?php

namespace App\Controller;

use App\Entity\Origin;
use App\Repository\OriginRepository;
use App\Services\UserOriginManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OriginController extends AbstractController
{
    /**
     * @var UserOriginManager
     */
    private $userOriginManager;
    /**
     * @var OriginRepository
     */
    private $originRepository;

    /**
     * OriginController constructor.
     *
     * @param UserOriginManager $userOriginManager
     * @param OriginRepository  $originRepository
     */
    public function __construct(UserOriginManager $userOriginManager, OriginRepository $originRepository)
    {
        $this->userOriginManager = $userOriginManager;
        $this->originRepository = $originRepository;
    }

    /**
     * @Route("/origin/{originId}/follow", name="origin_follow")
     *
     * @param string  $originId
     * @param Request $request
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function follow(string $originId, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $origin = $this->originRepository->findOneByOriginId($originId);
        $this->userOriginManager->follow($user, $origin);

        $this->addFlash('success', 'Following feed!');

        if ($request->headers->has('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('home_index');
    }

    /**
     * @Route("/origin/{originId}/unfollow", name="origin_unfollow")
     *
     * @param string  $originId
     * @param Request $request
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function unfollow(string $originId, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $origin = $this->originRepository->findOneByOriginId($originId);
        $this->userOriginManager->unfollow($user, $origin);

        $this->addFlash('success', 'Unfollowed feed!');

        if ($request->headers->has('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('home_index');
    }
}
