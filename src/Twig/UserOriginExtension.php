<?php

namespace App\Twig;

use App\Entity\Origin;
use App\Entity\UserOrigin;
use App\Repository\UserOriginRepository;
use Doctrine\ORM\NonUniqueResultException;
use FOS\UserBundle\Model\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserOriginExtension extends AbstractExtension
{
    /**
     * @var UserOriginRepository
     */
    private $userOriginRepository;

    /**
     * UserOriginExtension constructor.
     *
     * @param UserOriginRepository $userOriginRepository
     */
    public function __construct(UserOriginRepository $userOriginRepository)
    {
        $this->userOriginRepository = $userOriginRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('following', [$this, 'isFollowing']),
        ];
    }

    /**
     * @param UserInterface $user
     * @param Origin        $origin
     *
     * @return UserOrigin|null
     *
     * @throws NonUniqueResultException
     */
    public function isFollowing(UserInterface $user, Origin $origin)
    {
        return $this->userOriginRepository->findOneByUserAndOrigin($user, $origin);
    }
}
