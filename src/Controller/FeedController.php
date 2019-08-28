<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\Item;
use App\Message\IndexFeed;
use App\Repository\ItemRepository;
use App\Services\OriginManager;
use DateTime;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException as NonUniqueResultExceptionAlias;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FeedController extends AbstractController
{
    /**
     * @Route("/feed", name="feed_index")
     *
     * @return Response
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $feeds = $this->getDoctrine()->getRepository(Feed::class)->findAll();

        return $this->render('feed/index.html.twig', [
            'feeds' => $feeds,
        ]);
    }

    /**
     * @Route("/feed/create", name="feed_create")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createFormBuilder()
            ->add('link', UrlType::class, [
                'required' => true,
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $link = $form->get('link')->getData();

            $feed = $this->getDoctrine()->getRepository(Feed::class)->findOneBy([
                'baseUrl' => $link,
            ]);

            if ($feed instanceof Feed) {
                $this->addFlash('info', 'Feed already indexed, '.$link);

                return $this->redirectToRoute('feed_index');
            }

            try {
                $client = HttpClient::create();
                $response = $client->request('GET', $link);
                $crawler = new Crawler($response->getContent());

                $feed = new Feed();
                $feed->setBaseUrl($link);
                $feed->setTitle($crawler->filter('channel > title')->text());
                $feed->setDescription($crawler->filter('channel > description')->text());
                $feed->setLink($crawler->filter('channel > link')->text());
                if ($crawler->filter('channel > pubDate')->count()) {
                    $feed->setPubDate(new DateTime($crawler->filter('channel > pubDate')->text()));
                }
                if ($crawler->filter('channel > lastBuildDate')->count()) {
                    $feed->setLastBuildDate(new DateTime($crawler->filter('channel > lastBuildDate')->text()));
                }
                $this->getDoctrine()->getManager()->persist($feed);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Feed indexed');

                return $this->redirectToRoute('feed_index');
            } catch (ClientException $e) {
                $this->addFlash('warning', $e->getMessage());
            } catch (InvalidArgumentException $e) {
                $this->addFlash('warning', $e->getMessage());
            }
        }

        return $this->render('feed/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/feed/{feed}", name="feed_show")
     *
     * @param Feed          $feed
     * @param OriginManager $originManager
     * @param Request       $request
     *
     * @return Response
     *
     * @throws NonUniqueResultExceptionAlias
     */
    public function show(Feed $feed, OriginManager $originManager, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $origin = $originManager->getOriginByOriginInterface($feed);
        $items = $this->getItemRpository()->findByOrigin($origin, $request->query->getInt('page', 1));

        return $this->render('feed/show.html.twig', [
            'feed' => $feed,
            'origin' => $origin,
            'items' => $items,
        ]);
    }

    /**
     * @return ItemRepository|ObjectRepository
     */
    private function getItemRpository()
    {
        return $this->getDoctrine()->getRepository(Item::class);
    }

    /**
     * @Route("/feed/{feed}/handle", name="feed_handle")
     *
     * @param MessageBusInterface $messageBus
     * @param Feed                $feed
     *
     * @return Response
     */
    public function handle(MessageBusInterface $messageBus, Feed $feed)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $messageBus->dispatch(new IndexFeed($feed->getId()));

        $this->addFlash('success', 'Feed indexed');

        return $this->redirectToRoute('feed_show', [
            'feed' => $feed->getId(),
        ]);
    }
}
