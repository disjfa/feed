<?php

namespace App\Repository;

use App\Entity\Origin;
use App\Entity\OriginInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Origin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Origin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Origin[]    findAll()
 * @method Origin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OriginRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Origin::class);
    }

    /**
     * @param UserInterface $user
     * @param string        $origin
     *
     * @return Origin[]|Collection
     */
    public function findByUser(UserInterface $user, string $origin = null)
    {
        $qb = $this->createQueryBuilder('origin');
        $qb->join('origin.userOrigins', 'userOrigins');
        $qb->where('userOrigins.user = :user');
        $qb->setParameter('user', $user);
        if ($origin) {
            $qb->andWhere('origin.origin = :origin');
            $qb->setParameter('origin', $origin);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $originId
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function findOneByOriginId(string $originId)
    {
        $qb = $this->createQueryBuilder('origin');
        $qb->where('origin.originId = :originId');
        $qb->setParameter('originId', $originId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param OriginInterface $entity
     *
     * @return Origin|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByOrigin(OriginInterface $entity)
    {
        $qb = $this->createQueryBuilder('origin');
        $qb->where('origin.origin = :origin');
        $qb->andWhere('origin.originId = :originId');
        $qb->setParameter('origin', get_class($entity));
        $qb->setParameter('originId', $entity->getId());

        return $qb->getQuery()->getOneOrNullResult();
    }
}
