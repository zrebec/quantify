<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ChartExtension
 * @package App\Twig
 *
 * This class provides custom Twig functions for chart colors and saturation.
 */
class ChartExtension extends AbstractExtension {
    public function getFunctions() {
        return [
            new TwigFunction('chart_colors', [$this, 'getChartColors']),
            new TwigFunction('saturation_color', [$this, 'getSaturationColor']),
        ];
    }

    /**
     * Get the color based on saturation value.
     *
     * @param float $saturation The saturation value.
     * @return string The color class.
     */
    public function getSaturationColor(float $saturation): string {
        $colors = [
            1 => 'rgba(0, 128, 0, 0.8)',    // Green
            2 => 'rgba(0, 0, 255, 0.8)',    // Blue
            3 => 'rgba(255, 165, 0, 0.8)',  // Orange
            4 => 'rgba(128, 0, 128, 0.8)',  // Purple
            5 => 'rgba(255, 0, 0, 0.8)',    // Red
        ];
        return $colors[$saturation] ?? 'rgba(0, 0, 0, 0.8)'; // Default to black if saturation is not found
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