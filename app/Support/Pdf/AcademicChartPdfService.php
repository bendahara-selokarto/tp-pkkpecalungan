<?php

namespace App\Support\Pdf;

class AcademicChartPdfService
{
    private const CANVAS_WIDTH = 800;
    private const CANVAS_HEIGHT = 450;
    private const MARGIN_LEFT = 80;
    private const MARGIN_RIGHT = 40;
    private const MARGIN_TOP = 40;
    private const MARGIN_BOTTOM = 80;

    private const BAR_COLORS = [
        '#000000', // Black
        '#444444', // Dark Gray
        '#888888', // Medium Gray
        '#cccccc', // Light Gray
    ];

    /**
     * Generate an academic style vertical bar chart as a base64 encoded SVG string.
     */
    public function generateVerticalBarChartBase64(string $title, array $labels, array $series): string
    {
        $svg = $this->generateVerticalBarChart($title, $labels, $series);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate an academic style vertical bar chart SVG.
     */
    public function generateVerticalBarChart(string $title, array $labels, array $series): string
    {
        $maxValue = $this->calculateMaxValue($series);
        $scale = $this->calculateScale($maxValue);
        
        $chartWidth = self::CANVAS_WIDTH - self::MARGIN_LEFT - self::MARGIN_RIGHT;
        $chartHeight = self::CANVAS_HEIGHT - self::MARGIN_TOP - self::MARGIN_BOTTOM;
        
        $svg = $this->startSvg();
        $svg .= $this->drawBackground();
        $svg .= $this->drawTitle($title);
        $svg .= $this->drawGridAndAxes($scale, $chartWidth, $chartHeight);
        $svg .= $this->drawBars($labels, $series, $scale['limit'], $chartWidth, $chartHeight);
        $svg .= $this->drawLabels($labels, $chartWidth, $chartHeight);
        $svg .= $this->drawLegend(array_keys($series), $chartHeight);
        $svg .= $this->endSvg();

        return $svg;
    }

    private function calculateMaxValue(array $series): int
    {
        $max = 0;
        foreach ($series as $data) {
            foreach ($data as $val) {
                if ($val > $max) {
                    $max = $val;
                }
            }
        }
        return $max === 0 ? 10 : (int) $max;
    }

    private function calculateScale(int $maxValue): array
    {
        if ($maxValue <= 5) {
            $limit = 5;
            $step = 1;
        } elseif ($maxValue <= 10) {
            $limit = 10;
            $step = 2;
        } elseif ($maxValue <= 50) {
            $limit = 50;
            $step = 10;
        } elseif ($maxValue <= 100) {
            $limit = 100;
            $step = 20;
        } elseif ($maxValue <= 500) {
            $limit = ceil($maxValue / 50) * 50;
            $step = 50;
        } else {
            $limit = ceil($maxValue / 100) * 100;
            $step = $limit / 5;
        }

        $ticks = [];
        for ($i = 0; $i <= $limit; $i += $step) {
            $ticks[] = $i;
        }

        return [
            'limit' => $limit,
            'step' => $step,
            'ticks' => $ticks,
        ];
    }

    private function startSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . self::CANVAS_WIDTH . ' ' . self::CANVAS_HEIGHT . '" font-family="Arial, Helvetica, sans-serif">';
    }

    private function endSvg(): string
    {
        return '</svg>';
    }

    private function drawBackground(): string
    {
        return '<rect width="100%" height="100%" fill="#ffffff" />';
    }

    private function drawTitle(string $title): string
    {
        return '<text x="' . (self::CANVAS_WIDTH / 2) . '" y="25" font-size="16" font-weight="bold" text-anchor="middle" fill="#000000">' . htmlspecialchars($title) . '</text>';
    }

    private function drawGridAndAxes(array $scale, int $width, int $height): string
    {
        $svg = '';
        $limit = $scale['limit'];
        $ticks = $scale['ticks'];

        // Y Axis Grid and Ticks
        foreach ($ticks as $tick) {
            $y = self::MARGIN_TOP + $height - (($tick / $limit) * $height);
            
            // Grid line
            if ($tick > 0) {
                $svg .= '<line x1="' . self::MARGIN_LEFT . '" y1="' . $y . '" x2="' . (self::MARGIN_LEFT + $width) . '" y2="' . $y . '" stroke="#eeeeee" stroke-width="1" />';
            }

            // Tick mark
            $svg .= '<line x1="' . (self::MARGIN_LEFT - 5) . '" y1="' . $y . '" x2="' . self::MARGIN_LEFT . '" y2="' . $y . '" stroke="#000000" stroke-width="2" />';
            
            // Label
            $svg .= '<text x="' . (self::MARGIN_LEFT - 10) . '" y="' . ($y + 4) . '" font-size="12" text-anchor="end" fill="#000000">' . number_format($tick, 0, ',', '.') . '</text>';
        }

        // Main Axes
        // X Axis
        $svg .= '<line x1="' . self::MARGIN_LEFT . '" y1="' . (self::MARGIN_TOP + $height) . '" x2="' . (self::MARGIN_LEFT + $width) . '" y2="' . (self::MARGIN_TOP + $height) . '" stroke="#000000" stroke-width="2" />';
        // Y Axis
        $svg .= '<line x1="' . self::MARGIN_LEFT . '" y1="' . self::MARGIN_TOP . '" x2="' . self::MARGIN_LEFT . '" y2="' . (self::MARGIN_TOP + $height) . '" stroke="#000000" stroke-width="2" />';

        return $svg;
    }

    private function drawBars(array $labels, array $series, int $limit, int $width, int $height): string
    {
        $svg = '';
        $groupCount = count($labels);
        $seriesCount = count($series);
        
        if ($groupCount === 0) return '';

        $groupWidth = $width / $groupCount;
        $barPadding = 0.2; // 20% of group width for padding
        $availableWidth = $groupWidth * (1 - $barPadding);
        $barWidth = $availableWidth / $seriesCount;
        
        $seriesNames = array_keys($series);

        for ($i = 0; $i < $groupCount; $i++) {
            $groupX = self::MARGIN_LEFT + ($i * $groupWidth) + ($groupWidth * $barPadding / 2);
            
            for ($j = 0; $j < $seriesCount; $j++) {
                $val = (int) ($series[$seriesNames[$j]][$i] ?? 0);
                $barHeight = ($val / $limit) * $height;
                $barX = $groupX + ($j * $barWidth);
                $barY = self::MARGIN_TOP + $height - $barHeight;
                
                $color = self::BAR_COLORS[$j % count(self::BAR_COLORS)];
                
                $svg .= '<rect x="' . $barX . '" y="' . $barY . '" width="' . $barWidth . '" height="' . $barHeight . '" fill="' . $color . '" stroke="#000000" stroke-width="1" />';
            }
            
            // Tick mark on X axis
            $tickX = self::MARGIN_LEFT + (($i + 1) * $groupWidth);
            if ($i < $groupCount - 1) {
                $svg .= '<line x1="' . $tickX . '" y1="' . (self::MARGIN_TOP + $height) . '" x2="' . $tickX . '" y2="' . (self::MARGIN_TOP + $height + 5) . '" stroke="#000000" stroke-width="2" />';
            }
        }

        return $svg;
    }

    private function drawLabels(array $labels, int $width, int $height): string
    {
        $svg = '';
        $groupCount = count($labels);
        if ($groupCount === 0) return '';

        $groupWidth = $width / $groupCount;

        for ($i = 0; $i < $groupCount; $i++) {
            $x = self::MARGIN_LEFT + ($i * $groupWidth) + ($groupWidth / 2);
            $y = self::MARGIN_TOP + $height + 15;
            
            $svg .= '<g transform="translate(' . $x . ',' . $y . ')">';
            $svg .= '<text transform="rotate(-45)" font-size="10" text-anchor="end" fill="#000000">' . htmlspecialchars($labels[$i]) . '</text>';
            $svg .= '</g>';
        }

        return $svg;
    }

    private function drawLegend(array $seriesNames, int $height): string
    {
        $svg = '<g transform="translate(' . self::MARGIN_LEFT . ',' . (self::MARGIN_TOP + $height + 60) . ')">';
        
        $xOffset = 0;
        foreach ($seriesNames as $idx => $name) {
            $color = self::BAR_COLORS[$idx % count(self::BAR_COLORS)];
            
            $svg .= '<rect x="' . $xOffset . '" y="0" width="12" height="12" fill="' . $color . '" stroke="#000000" stroke-width="1" />';
            $svg .= '<text x="' . ($xOffset + 18) . '" y="10" font-size="11" fill="#000000">' . htmlspecialchars($name) . '</text>';
            
            // Approximate width for next item
            $xOffset += (strlen($name) * 7) + 40;
        }
        
        $svg .= '</g>';
        return $svg;
    }
}
