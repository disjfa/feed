<?php

namespace App\Services;

use App\Entity\Feed;
use App\Entity\Origin;
use App\Repository\OriginRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class OriginService
{
    /**
     * @var OriginRepository
     */
    private $originRepository;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * LikeService constructor.
     */
    public function __construct(OriginRepository $originRepository, Security $security)
    {
        $this->originRepository = $originRepository;
        $this->user = $security->getUser();
    }

    /**
     * @return Origin[]|Collection
     */
    public function userOrigins()
    {
        if (null === $this->user) {
            return new ArrayCollection();
        }

        return $this->originRepository->findByUser($this->user, Feed::class);
    }
}
