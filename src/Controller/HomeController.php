<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use App\Repository\OriginRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     *
     * @return Response
     *
     * @throws Exception
     */
    public function index(ItemRepository $itemRepository, OriginRepository $originRepository, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $items = $itemRepository->findFollowing($this->getUser(), $request->query->getInt('page', 1));

        if (0 === count($items)) {
            return $this->render('home/welcome.html.twig');
        }

        return $this->render('home/index.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/hello", name="home_hello")
     */
    public function hello()
    {
        return $this->render('home/hello.html.twig');
    }
}
