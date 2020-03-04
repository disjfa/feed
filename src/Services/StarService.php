<?php

namespace App\Services;

use App\Entity\Item;
use App\Entity\Star;
use App\Repository\StarRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class StarService
{
    /**
     * @var UserInterface|null
     */
    private $user;
    /**
     * @var StarRepository
     */
    private $starRepository;

    /**
     * LikeService constructor.
     * @param Security $security
     * @param StarRepository $starRepository
     */
    public function __construct(Security $security, StarRepository $starRepository)
    {
        $this->user = $security->getUser();
        $this->starRepository = $starRepository;
    }

    /**
     * @param Item $item
     * @return bool
     * @throws NonUniqueResultException
     */
    public function isStarred(Item $item): bool
    {
        $star = $this->starRepository->findOneByItemAndUser($item, $this->user);
        return $star instanceof Star;
    }
}
