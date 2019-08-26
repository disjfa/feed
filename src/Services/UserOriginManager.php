<?php

namespace App\Services;

use App\Entity\Origin;
use App\Entity\UserOrigin;
use App\Repository\UserOriginRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserOriginManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserOriginRepository
     */
    private $userOriginRepository;

    /**
     * UserOriginManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserOriginRepository   $userOriginRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserOriginRepository $userOriginRepository)
    {
        $this->entityManager = $entityManager;
        $this->userOriginRepository = $userOriginRepository;
    }

    /**
     * @param UserInterface $user
     * @param Origin        $origin
     *
     * @throws NonUniqueResultException
     */
    public function follow(UserInterface $user, Origin $origin)
    {
        $userOrigin = $this->userOriginRepository->findOneByUserAndOrigin($user, $origin);
        if (null === $userOrigin) {
            $userOrigin = new UserOrigin();
            $userOrigin->setUser($user);
            $userOrigin->setOrigin($origin);
            $this->entityManager->persist($userOrigin);
            $this->entityManager->flush($userOrigin);
        }
    }

    /**
     * @param UserInterface $user
     * @param Origin        $origin
     *
     * @throws NonUniqueResultException
     */
    public function unfollow($user, Origin $origin)
    {
        $userOrigin = $this->userOriginRepository->findOneByUserAndOrigin($user, $origin);
        if ($userOrigin instanceof UserOrigin) {
            $this->entityManager->remove($userOrigin);
            $this->entityManager->flush();
        }
    }
}
