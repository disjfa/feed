<?php

namespace App\Twig;

use App\Entity\OriginInterface;
use App\Entity\UserOrigin;
use App\Repository\UserOriginRepository;
use App\Services\OriginManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserOriginExtension extends AbstractExtension
{
    /**
     * @var UserOriginRepository
     */
    private $userOriginRepository;
    /**
     * @var OriginManager
     */
    private $originManager;

    /**
     * UserOriginExtension constructor.
     */
    public function __construct(UserOriginRepository $userOriginRepository, OriginManager $originManager)
    {
        $this->userOriginRepository = $userOriginRepository;
        $this->originManager = $originManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('following', [$this, 'isFollowing']),
        ];
    }

    /**
     * @return UserOrigin|null
     *
     * @throws NonUniqueResultException
     */
    public function isFollowing(UserInterface $user, OriginInterface $originInterface)
    {
        $origin = $this->originManager->getOriginByOriginInterface($originInterface);

        return $this->userOriginRepository->findOneByUserAndOrigin($user, $origin);
    }
}
