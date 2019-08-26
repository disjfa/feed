<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Origin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findFollowing(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('item');
        $qb->join('item.origins', 'origin');
        $qb->join('origin.userOrigins', 'userOrigins');
        $qb->where('userOrigins.user = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('item.pubDate', 'DESC');
        $qb->setMaxResults(25);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Origin $origin
     *
     * @return Item[]
     */
    public function findByOrigin(Origin $origin)
    {
        $qb = $this->createQueryBuilder('item');
        $qb->join('item.origins', 'origin');
        $qb->where('origin = :origin');
        $qb->setParameter('origin', $origin);
        $qb->orderBy('item.pubDate', 'DESC');
        $qb->setMaxResults(25);

        return $qb->getQuery()->getResult();
    }
}
