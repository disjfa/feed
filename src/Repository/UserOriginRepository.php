<?php

namespace App\Repository;

use App\Entity\Origin;
use App\Entity\UserOrigin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserOrigin|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserOrigin|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserOrigin[]    findAll()
 * @method UserOrigin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOriginRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOrigin::class);
    }

    /**
     * @return UserOrigin|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByUserAndOrigin(UserInterface $user, Origin $origin)
    {
        $qb = $this->createQueryBuilder('user_origin');
        $qb->where('user_origin.user = :user');
        $qb->andWhere('user_origin.origin = :origin');
        $qb->setParameter('user', $user);
        $qb->setParameter('origin', $origin);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
