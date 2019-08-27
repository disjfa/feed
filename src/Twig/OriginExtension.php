<?php

namespace App\Twig;

use App\Entity\Feed;
use App\Entity\Origin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OriginExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * OriginExtension constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('origin_badge', [$this, 'getOriginLink'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param Origin $origin
     *
     * @return string
     */
    public function getOriginLink(Origin $origin)
    {
        $entity = $this->entityManager->getRepository($origin->getOrigin())->find($origin->getOriginId());
        if (null === $entity) {
            return $origin->getId();
        }

        $url = '#';
        if ($entity instanceof Feed) {
            $url = $this->router->generate('feed_show', [
                'feed' => $entity->getId(),
            ]);
        }

        return '<a href="'.$url.'" class="badge badge-dark">'.(string) $entity.'</a>';
    }
}
