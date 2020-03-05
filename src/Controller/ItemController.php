<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\Item;
use App\Entity\Star;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Repository\OriginRepository;
use App\Repository\StarRepository;
use App\Services\OriginManager;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class ItemController extends AbstractController
{
    /**
     * @Route("/starred", name="item_starred")
     *
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
     * @Route("/item/{item}", name="item_view")
     *
     * @return Response
     *
     * @throws Exception
     */
    public function view(Item $item, OriginRepository $originRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $origins = $originRepository->findByUser($this->getUser(), Feed::class);

        return $this->render('item/item.html.twig', [
            'item' => $item,
            'origins' => $origins,
        ]);
    }

    /**
     * @Route("/post", name="item_post")
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function post(Request $request, OriginRepository $originRepository, OriginManager $originManager, RouterInterface $router)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && false === $request->request->has('preview')) {
            $entityManager = $this->getDoctrine()->getManager();

            $origin = $originManager->getOriginByOriginInterface($this->getUser());
            $item->addOrigin($origin);

            $route = $router->generate('item_view', ['item' => $item->getId()]);
            $item->setGuid($route);
            $item->setLink($route);
            $item->setPubDate(new DateTime('now'));

            $entityManager->persist($item);
            $entityManager->flush();

            $this->addFlash('success', 'Item added');

            return $this->redirect($route);
        }

        $origins = $originRepository->findByUser($this->getUser(), Feed::class);

        return $this->render('item/form.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'origins' => $origins,
        ]);
    }

    /**
     * @Route("/item/{item}/star", name="item_star")
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function star(Item $item, Request $request, StarRepository $starRepository)
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

        $json = 'application/json' === $request->headers->get('Content-Type');
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
