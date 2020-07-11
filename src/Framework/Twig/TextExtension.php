<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    public function excerpt($content, $maxLength = 100): string
    {
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($content, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
