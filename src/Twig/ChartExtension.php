<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ChartExtension extends AbstractExtension {
    public function getFunctions() {
        return [
            new TwigFunction('chart_colors', [$this, 'getChartColors']),
        ];
    }

    public function getChartColors(): array {
        return ['color-1', 'color-2', 'color-3', 'color-4', 'color-5'];
    }
}