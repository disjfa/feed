<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('truncate', [$this, 'truncate']),
        ];
    }

    public function truncate($string, $limit, $separator = '...')
    {
        if (strlen($string) > $limit) {
            $newLimit = $limit - strlen($separator);
            $s = substr($string, 0, $newLimit + 1);

            return substr($s, 0, strrpos($s, ' ')).$separator;
        }

        return $string;
    }
}
