<?php

namespace App\Controller;


use App\Entity\Feed;
use App\Entity\Item;
use App\Entity\Star;
use App\Repository\ItemRepository;
use App\Repository\OriginRepository;
use App\Repository\StarRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    /**
     * @Route("/starred", name="item_starred")
     *
     * @param Request $request
     * @param ItemRepository $itemRepository
     * @param OriginRepository $originRepository
     * @return Response
     */
    public function starred(Request $request, ItemRepository $itemRepository, OriginRepository $originRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $items = $itemRepository->findStarred($this->getUser(), $request->query->getInt('page', 1));
        $origins = $originRepository->findByUser($this->getUser(), Feed::class);

        return $this->render('item/starred.html.twig', [
            'items' => $items,
            'origins' => $origins,
        ]);

    }

    /**
     * @Route("/like/{item}", name="item_like")
     *
     * @param Item $item
     * @param Request $request
     * @param StarRepository $starRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function index(Item $item, Request $request, StarRepository $starRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $star = $starRepository->findOneByItemAndUser($item, $this->getUser());
        $starred = false;
        $entityManager = $this->getDoctrine()->getManager();
        if (null === $star) {
            $star = new Star($item, $this->getUser());
            $entityManager->persist($star);
            $starred = true;

        } else {
            $entityManager->remove($star);
        }

        $json = $request->headers->get('Content-Type') === 'application/json';
        $entityManager->flush();
        if ($starred) {
            if ($json) {
                return new JsonResponse([
                    'starred' => true,
                ]);
            }
            $this->addFlash('success', 'starred');
        } else {
            if ($json) {
                return new JsonResponse([
                    'starred' => false,
                ]);
            }
            $this->addFlash('success', 'unstarred');
        }

        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->redirectToRoute('home_index');
    }
}
