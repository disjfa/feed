<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Star;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Star|null find($id, $lockMode = null, $lockVersion = null)
 * @method Star|null findOneBy(array $criteria, array $orderBy = null)
 * @method Star[]    findAll()
 * @method Star[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StarRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Star::class);
    }

    /**
     * @param Item $item
     * @param UserInterface $user
     * @return Star|null
     * @throws NonUniqueResultException
     */
    public function findOneByItemAndUser(Item $item, UserInterface $user)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->where('l.item = :item');
        $qb->andWhere('l.user = :user');
        $qb->setParameter('item', $item);
        $qb->setParameter('user', $user);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
