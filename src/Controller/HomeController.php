<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     *
     * @param ItemRepository $itemRepository
     *
     * @return Response
     */
    public function index(ItemRepository $itemRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $items = $itemRepository->findFollowing($this->getUser());

        return $this->render('home/index.html.twig', [
            'items' => $items,
        ]);
    }
}
