<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Repository\ItemRepository;
use App\Repository\OriginRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     *
     * @param ItemRepository   $itemRepository
     * @param OriginRepository $originRepository
     * @param Request          $request
     *
     * @return Response
     */
    public function index(ItemRepository $itemRepository, OriginRepository $originRepository, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $items = $itemRepository->findFollowing($this->getUser(), $request->query->getInt('page', 1));
        $origins = $originRepository->findByUser($this->getUser(), Feed::class);
        if (0 === count($items)) {
            return $this->render('home/welcome.html.twig');
        }

        return $this->render('home/index.html.twig', [
            'items' => $items,
            'origins' => $origins,
        ]);
    }
}
