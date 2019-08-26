<?php

namespace App\Services;

use App\Entity\Origin;
use App\Entity\OriginInterface;
use App\Repository\OriginRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class OriginManager
{
    /**
     * @var OriginRepository
     */
    private $originRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param OriginRepository       $originRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(OriginRepository $originRepository, EntityManagerInterface $entityManager)
    {
        $this->originRepository = $originRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param OriginInterface $originInterface
     *
     * @return Origin|OriginInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function getOriginByOriginInterface(OriginInterface $originInterface)
    {
        $origin = $this->originRepository->findOneByOrigin($originInterface);
        if ($origin instanceof Origin) {
            return $origin;
        }

        $origin = new Origin();
        $origin->setOriginId($originInterface->getId());
        $origin->setOrigin(get_class($originInterface));

        $this->entityManager->persist($origin);
        $this->entityManager->flush($origin);

        return $origin;
    }
}
