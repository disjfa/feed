<?php

namespace App\Services\Feed;

use App\Entity\Feed;
use App\Entity\Item;
use App\Entity\Origin;
use App\Message\ItemWasCreated;
use App\Repository\ItemRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use DOMElement;
use DOMNode;
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;

class ChannelIndexer implements IndexerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * ChannelIndexer constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, ItemRepository $itemRepository, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->itemRepository = $itemRepository;
        $this->messageBus = $messageBus;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function index(DOMNode $doc, Feed $feed, Origin $origin)
    {
        foreach ($doc->childNodes as $childNode) {
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
     * @return Item|void|null
     *
     * @throws Exception
     */
    public function getItem(DOMNode $element)
    {
        $guid = false;
        foreach ($element->childNodes as $childNode) {
            /** @var DOMElement $childNode */
            if ('guid' === $childNode->nodeName) {
                $guid = trim($childNode->textContent);
            }
            if ('link' === $childNode->nodeName && false === $guid) {
                $guid = trim($childNode->textContent);
            }
        }

        if ($guid) {
            $item = $this->itemRepository->findOneBy([
                'guid' => $guid,
            ]);
            if (null === $item) {
                $item = new Item();
                $item->setGuid($guid);
            }

            return $item;
        }

        return;
    }

    /**
     * @throws Exception
     */
    public function indexItem(DOMElement $element, Origin $origin)
    {
        $item = $this->getItem($element);
        if (false === $item instanceof Item) {
            return;
        }

        $item->addOrigin($origin);
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
