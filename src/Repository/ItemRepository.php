<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Origin;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Item::class);
        $this->paginator = $paginator;
    }

    public function findStarred(UserInterface $user, int $page = 1)
    {
        $date = new DateTime('now');

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.stars', 'stars');
        $qb->where('stars.user = :user');
        $qb->setParameter('user', $user);
        $qb->andWhere('item.pubDate < :date');
        $qb->setParameter('date', $date);
        $qb->orderBy('item.pubDate', 'DESC');

        return $this->paginator->paginate($qb, $page, 36);
    }

    /**
     * @return PaginationInterface|Item[]
     *
     * @throws Exception
     */
    public function findFollowing(UserInterface $user, int $page = 1)
    {
        $date = new DateTime('now');

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.origins', 'origin');
        $qb->join('origin.userOrigins', 'userOrigins');
        $qb->where('userOrigins.user = :user');
        $qb->setParameter('user', $user);
        $qb->andWhere('item.pubDate < :date');
        $qb->setParameter('date', $date);
        $qb->orderBy('item.pubDate', 'DESC');

        return $this->paginator->paginate($qb, $page, 36);
    }

    /**
     * @return PaginationInterface|Item[]
     */
    public function findByOrigin(Origin $origin, int $page = 1)
    {
        $qb = $this->createQueryBuilder('item');
        $qb->join('item.origins', 'origin');
        $qb->where('origin = :origin');
        $qb->setParameter('origin', $origin);
        $qb->orderBy('item.pubDate', 'DESC');

        return $this->paginator->paginate($qb, $page, 36);
    }
}
