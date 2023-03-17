<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    final public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    final public function ago(DateTime $date, string $format = 'd/m/Y H:i'): string
    {
        return '<span class="timeago" datetime="' . $date->format('Y-m-d H:i:s') . '">' .
            $date->format($format) .
            '</span>';
    }
}
