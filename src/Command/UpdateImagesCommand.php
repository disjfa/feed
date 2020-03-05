<?php

namespace App\Command;

use App\Entity\Item;
use App\Message\ItemWasCreated;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateImagesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:update:images';
    /**
     * @var ItemRepository
     */
    private $itemRepository;
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
    public function __construct(ItemRepository $itemRepository, MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        parent::__construct(self::$defaultName);

        $this->itemRepository = $itemRepository;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->setDescription('Update images');
    }

    /**
     * @return int|void|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('imageUrl'));
        $criteria->orderBy(['pubDate' => Criteria::DESC]);
        $items = $this->itemRepository->matching($criteria);

        foreach ($items as $item) {
            /* @var Item $item */
            $this->logger->info('Checking '.$item->getTitle());

            $this->messageBus->dispatch(new ItemWasCreated($item->getId()));
        }

        $this->logger->info('Done indexing');
    }
}
