<?php

namespace App\MessageHandler;

use App\Message\IndexFeed;
use App\Repository\FeedRepository;
use App\Services\Feed\FeedManager;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class IndexFeedHandler implements MessageHandlerInterface
{
    /**
     * @var FeedRepository
     */
    private $feedRepository;
    /**
     * @var FeedManager
     */
    private $feedManager;

    /**
     * IndexFeedHandler constructor.
     *
     * @param FeedRepository $feedRepository
     * @param FeedManager    $feedManager
     */
    public function __construct(
        FeedRepository $feedRepository,
        FeedManager $feedManager
    ) {
        $this->feedRepository = $feedRepository;
        $this->feedManager = $feedManager;
    }

    /**
     * @param IndexFeed $indexFeed
     *
     * @return bool
     *
     * @throws Exception
     */
    public function __invoke(IndexFeed $indexFeed)
    {
        $feed = $this->feedRepository->find($indexFeed->getFeedId());
        $this->feedManager->indexFeed($feed);

        return true;
    }
}
