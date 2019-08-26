<?php

namespace App\Repository;

use App\Entity\Feed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Feed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feed[]    findAll()
 * @method Feed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRepository extends ServiceEntityRepository
{
    /**
     * FeedRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    /**
     * @return Feed[]|Collection
     *
     * @throws Exception
     */
    public function findFeedsToIndex()
    {
        $indexDate = new \DateTime('-1 hour');

        $qb = $this->createQueryBuilder('feed');
        $qb->where('feed.indexed < :indexed');
        $qb->setParameter('indexed', $indexDate);

        return $qb->getQuery()->getResult();
    }
}
