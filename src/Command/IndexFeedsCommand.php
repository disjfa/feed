<?php

namespace App\Command;

use App\Message\IndexFeed;
use App\Repository\FeedRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class IndexFeedsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:index:feeds';
    /**
     * @var FeedRepository
     */
    private $feedRepository;
    /**
     * @var MessageBusInterface
     */
    private $messageBus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * IndexFeedsCommand constructor.
     */
    public function __construct(FeedRepository $feedRepository, MessageBusInterface $messageBus, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct(self::$defaultName);

        $this->feedRepository = $feedRepository;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Index feeds');
    }

    /**
     * @return int|void|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feeds = $this->feedRepository->findFeedsToIndex();
        $indexedDate = new DateTime();
        foreach ($feeds as $feed) {
            $this->logger->info('Indexing '.$feed->getTitle());
            $this->messageBus->dispatch(new IndexFeed($feed->getId()));

            $feed->setIndexed($indexedDate);
            $this->entityManager->persist($feed);
            $this->entityManager->flush($feed);
        }
        $this->logger->info('Done indexing');
    }
}
