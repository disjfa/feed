<?php

namespace App\Services\Feed;

use App\Entity\Feed;
use App\Services\OriginManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use DOMDocument;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FeedManager
{
    /**
     * @var ChannelIndexer
     */
    private $channelIndexer;
    /**
     * @var OriginManager
     */
    private $originManager;
    /**
     * @var FeedIndexer
     */
    private $feedIndexer;

    /**
     * FeedManager constructor.
     */
    public function __construct(ChannelIndexer $channelIndexer, FeedIndexer $feedIndexer, OriginManager $originManager)
    {
        $this->channelIndexer = $channelIndexer;
        $this->originManager = $originManager;
        $this->feedIndexer = $feedIndexer;
    }

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function indexFeed(Feed $feed)
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $feed->getBaseUrl());

        $doc = new DOMDocument();
        $doc->loadXML($response->getContent());

        $origin = $this->originManager->getOriginByOriginInterface($feed);

        $channels = $doc->getElementsByTagName('channel');
        if (1 === $channels->count()) {
            $this->channelIndexer->index($channels->item(0), $feed, $origin);
        }

        $feeds = $doc->getElementsByTagName('feed');
        if (1 === $feeds->count()) {
            $this->feedIndexer->index($feeds->item(0), $feed, $origin);
        }
    }
}
