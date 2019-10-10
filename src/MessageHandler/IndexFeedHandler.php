<?php

namespace App\MessageHandler;

use App\Entity\Item;
use App\Entity\Origin;
use App\Message\IndexFeed;
use App\Message\ItemWasCreated;
use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
use App\Services\OriginManager;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use DOMDocument;
use DOMElement;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class IndexFeedHandler.
 */
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
     * @var OriginManager
     */
    private $originManager;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * IndexFeedHandler constructor.
     *
     * @param FeedRepository         $feedRepository
     * @param ItemRepository         $itemRepository
     * @param EntityManagerInterface $entityManager
     * @param OriginManager          $originManager
     * @param MessageBusInterface    $messageBus
     */
    public function __construct(
        FeedRepository $feedRepository,
        ItemRepository $itemRepository,
        EntityManagerInterface $entityManager,
        OriginManager $originManager,
        MessageBusInterface $messageBus
    ) {
        $this->feedRepository = $feedRepository;
        $this->itemRepository = $itemRepository;
        $this->entityManager = $entityManager;
        $this->originManager = $originManager;
        $this->messageBus = $messageBus;
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

        $doc = new DOMDocument();
        $doc->loadXML($response->getContent());
        $channels = $doc->getElementsByTagName('channel');
        if (1 !== $channels->count()) {
            return;
        }

        $origin = $this->originManager->getOriginByOriginInterface($feed);
        $channel = $channels->item(0);
        foreach ($channel->childNodes as $childNode) {
            /** @var DOMElement $childNode */
            if ('title' === $childNode->nodeName) {
                $feed->setTitle(trim($childNode->nodeValue));
            }
            if ('description' === $childNode->nodeName) {
                $feed->setDescription(trim($childNode->nodeValue));
            }
            if ('link' === $childNode->nodeName) {
                $feed->setLink(trim($childNode->nodeValue));
            }
            if ('pubDate' === $childNode->nodeName) {
                $feed->setPubDate(new DateTime($childNode->nodeValue));
            }
            if ('lastBuildDate' === $childNode->nodeName) {
                $feed->setLastBuildDate(new DateTime($childNode->nodeValue));
            }
            if ('item' === $childNode->nodeName) {
                $this->indexItem($childNode, $origin);
            }
        }

        $this->entityManager->persist($feed);
        $this->entityManager->flush();
    }

    /**
     * @param DOMElement $element
     * @param Origin     $origin
     *
     * @return Item|void|null
     *
     * @throws Exception
     */
    public function getItem(DOMElement $element, Origin $origin)
    {
        foreach ($element->childNodes as $childNode) {
            /** @var DOMElement $childNode */
            if ('guid' === $childNode->nodeName) {
                $guid = trim($childNode->textContent);
                $item = $this->itemRepository->findOneBy([
                    'guid' => $guid,
                ]);
                if (null === $item) {
                    $item = new Item();
                    $item->addOrigin($origin);
                    $item->setGuid($guid);
                }

                return $item;
            }
        }

        return;
    }

    /**
     * @param DOMElement $element
     * @param Origin     $origin
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function indexItem(DOMElement $element, Origin $origin)
    {
        $item = $this->getItem($element, $origin);
        if (false === $item instanceof Item) {
            return;
        }

        foreach ($element->childNodes as $childNode) {
            /** @var DOMElement $childNode */
            if ('pubDate' === $childNode->nodeName) {
                $pubDate = new DateTime($childNode->textContent);
                $pubDate->setTimezone(new DateTimeZone('UTC'));
                $item->setPubDate($pubDate);
            }
            if ('title' === $childNode->nodeName) {
                $item->setTitle(trim($childNode->textContent));
            }
            if ('description' === $childNode->nodeName) {
                $item->setDescription(trim($childNode->textContent));
            }
            if ('link' === $childNode->nodeName) {
                $item->setLink(trim($childNode->textContent));
            }
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new ItemWasCreated($item->getId()));
    }
}
