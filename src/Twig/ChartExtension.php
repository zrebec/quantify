<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ChartExtension
 * @package App\Twig
 *
 * This class provides custom Twig functions for chart colors and level.
 */
class ChartExtension extends AbstractExtension {
    public function getFunctions() {
        return [
            new TwigFunction('chart_colors', [$this, 'getChartColors']),
            new TwigFunction('level_color', [$this, 'getLevelColor']),
        ];
    }

    /**
     * Get the color based on level value.
     *
     * @param float $level The level value.
     * @return string The color class.
     */
    public function getLevelColor(float $level): string {
        $colors = [
            1 => 'rgba(0, 128, 0, 0.8)',    // Green
            2 => 'rgba(0, 0, 255, 0.8)',    // Blue
            3 => 'rgba(255, 165, 0, 0.8)',  // Orange
            4 => 'rgba(128, 0, 128, 0.8)',  // Purple
            5 => 'rgba(255, 0, 0, 0.8)',    // Red
        ];
        return $colors[$level] ?? 'rgba(0, 0, 0, 0.8)'; // Default to black if level is not found
    }

    /**
     * Get the chart colors.
     *
     * @return array The array of color classes.
     */
    public function getChartColors(): array {
        return ['color-1', 'color-2', 'color-3', 'color-4', 'color-5'];
    }
}