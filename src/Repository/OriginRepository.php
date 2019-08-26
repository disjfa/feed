<?php

namespace App\Repository;

use App\Entity\Origin;
use App\Entity\OriginInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

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
