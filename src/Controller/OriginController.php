<?php

namespace App\Controller;

use App\Entity\Origin;
use App\Services\UserOriginManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OriginController extends AbstractController
{
    /**
     * @Route("/origin/{origin}/follow", name="origin_follow")
     *
     * @param Origin            $origin
     * @param Request           $request
     * @param UserOriginManager $userOriginManager
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function follow(Origin $origin, Request $request, UserOriginManager $userOriginManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $userOriginManager->follow($user, $origin);

        $this->addFlash('success', 'Following feed!');

        if ($request->headers->has('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('home_index');
    }

    /**
     * @Route("/origin/{origin}/unfollow", name="origin_unfollow")
     *
     * @param Origin            $origin
     * @param Request           $request
     * @param UserOriginManager $userOriginManager
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function unfollow(Origin $origin, Request $request, UserOriginManager $userOriginManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $userOriginManager->unfollow($user, $origin);

        $this->addFlash('success', 'Unfollowed feed!');

        if ($request->headers->has('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('home_index');
    }
}
