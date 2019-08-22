<?php

namespace App\MessageHandler;

use App\Entity\Feed;
use App\Entity\Item;
use App\Message\IndexFeed;
use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use DOMElement;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class IndexFeedHandler implements MessageHandlerInterface
{
    /**
     * @var FeedRepository
     */
    private $feedRepository;
    /**
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * IndexFeedHandler constructor.
     *
     * @param FeedRepository $feedRepository
     * @param ItemRepository $itemRepository
     * @param EntityManager  $entityManager
     */
    public function __construct(FeedRepository $feedRepository, ItemRepository $itemRepository, EntityManagerInterface $entityManager)
    {
        $this->feedRepository = $feedRepository;
        $this->itemRepository = $itemRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param IndexFeed $indexFeed
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function __invoke(IndexFeed $indexFeed)
    {
        $feed = $this->feedRepository->find($indexFeed->getFeedId());

        $client = HttpClient::create();
        $response = $client->request('GET', $feed->getBaseUrl());

        $crawler = new Crawler($response->getContent());
        $items = $crawler->filter('channel item');
        foreach ($items as $item) {
            $pubDate = null;
            foreach ($item->childNodes as $k => $n) {
                if ($n instanceof DOMElement && 'pubDate' === $n->nodeName) {
                    $pubDate = new DateTime($n->nodeValue);
                }
            }

            $aa = new Crawler($item);
            $title = $aa->filter('title')->text();
            $description = $aa->filter('description')->text();
            $guid = $aa->filter('guid')->text();

            $item = $this->itemRepository->findOneBy([
                'guid' => $guid,
            ]);
            if (null === $item) {
                $item = new Item();
            }

            $item->setPubDate($pubDate);
            $item->setTitle($title);
            $item->setDescription($description);
            $item->setGuid($guid);
            $item->setOrigin(Feed::class);
            $item->setOriginId($feed->getId());
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }
    }
}
