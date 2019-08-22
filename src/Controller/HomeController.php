<?php

namespace App\Controller;

use App\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     */
    public function index()
    {
        $items = $this->getDoctrine()->getRepository(Item::class)->findBy([], ['pubDate' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'items' => $items,
        ]);
    }
}
