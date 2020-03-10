<?php

namespace App\Twig;

use League\CommonMark\CommonMarkConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    /**
     * @var CommonMarkConverter
     */
    private $commonMarkConverter;

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function getConverter()
    {
        if (null === $this->commonMarkConverter) {
            $this->commonMarkConverter = new CommonMarkConverter([
                'html_input' => 'escape',
                'allow_unsafe_links' => false,
            ]);
        }

        return $this->commonMarkConverter;
    }

    public function markdown($value)
    {
        return $this->getConverter()->convertToHtml($value);
    }
}
