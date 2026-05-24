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

    public const ACADEMIC_PALETTE = [
        'blue' => '#1d4ed8',    // Blue-700
        'green' => '#15803d',   // Green-700
        'yellow' => '#eab308',  // Yellow-600
        'red' => '#b91c1c',     // Red-700
        'orange' => '#ea580c',  // Orange-600
        'purple' => '#7e22ce',  // Purple-700
        'cyan' => '#0e7490',    // Cyan-700
        'pink' => '#be185d',    // Pink-700
        'slate' => '#334155',   // Slate-700
    ];

    public const ROLE_COLORS = [
        'pokja-i' => self::ACADEMIC_PALETTE['blue'],
        'pokja-ii' => self::ACADEMIC_PALETTE['green'],
        'pokja-iii' => self::ACADEMIC_PALETTE['yellow'],
        'pokja-iv' => self::ACADEMIC_PALETTE['red'],
        'sekretaris' => self::ACADEMIC_PALETTE['purple'],
        'bendahara' => self::ACADEMIC_PALETTE['orange'],
        'admin' => self::ACADEMIC_PALETTE['slate'],
    ];

    private const BAR_COLORS = [
        self::ACADEMIC_PALETTE['blue'],
        self::ACADEMIC_PALETTE['green'],
        self::ACADEMIC_PALETTE['yellow'],
        self::ACADEMIC_PALETTE['red'],
        self::ACADEMIC_PALETTE['orange'],
        self::ACADEMIC_PALETTE['purple'],
        self::ACADEMIC_PALETTE['cyan'],
        self::ACADEMIC_PALETTE['pink'],
        self::ACADEMIC_PALETTE['slate'],
    ];

    /**
     * Generate an academic style vertical bar chart as a base64 encoded SVG string with optional color mapping.
     */
    public function generateVerticalBarChartBase64(string $title, array $labels, array $series, array $colorMap = [], ?string $role = null): string
    {
        $svg = $this->generateVerticalBarChart($title, $labels, $series, $colorMap, $role);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate an academic style vertical bar chart SVG with optional color mapping.
     */
    public function generateVerticalBarChart(string $title, array $labels, array $series, array $colorMap = [], ?string $role = null): string
    {
        $maxValue = $this->calculateMaxValue($series);
        $scale = $this->calculateScale($maxValue);
        
        $chartWidth = self::CANVAS_WIDTH - self::MARGIN_LEFT - self::MARGIN_RIGHT;
        $chartHeight = self::CANVAS_HEIGHT - self::MARGIN_TOP - self::MARGIN_BOTTOM;
        
        $svg = $this->startSvg();
        $svg .= $this->drawBackground();
        $svg .= $this->drawTitle($title);
        $svg .= $this->drawGridAndAxes($scale, $chartWidth, $chartHeight);
        $svg .= $this->drawBars($labels, $series, $scale['limit'], $chartWidth, $chartHeight, $colorMap, $role);
        $svg .= $this->drawLabels($labels, $chartWidth, $chartHeight);
        $svg .= $this->drawLegend(array_keys($series), $chartHeight, $colorMap, $role);
        $svg .= $this->endSvg();

        return $svg;
    }

    /**
     * Get a standardized color mapping for Pokja classifications.
     */
    public function getPokjaColorMap(): array
    {
        return [
            // UP2K / Posyandu levels
            'Pemula' => self::ACADEMIC_PALETTE['red'],
            'Pratama' => self::ACADEMIC_PALETTE['red'],
            'Madya' => self::ACADEMIC_PALETTE['yellow'],
            'Utama' => self::ACADEMIC_PALETTE['green'],
            'Purnama' => self::ACADEMIC_PALETTE['green'],
            'Mandiri' => self::ACADEMIC_PALETTE['blue'],
            'Istimewa' => self::ACADEMIC_PALETTE['blue'],
        ];
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

    private function drawBars(array $labels, array $series, int $limit, int $width, int $height, array $colorMap = [], ?string $role = null): string
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
                
                $color = $this->resolveColor($seriesNames[$j], $j, $colorMap, $role);
                
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

    private function resolveColor(string $name, int $index, array $colorMap, ?string $role = null): string
    {
        // 1. Check exact match in color map (e.g., specific levels)
        if (isset($colorMap[$name])) {
            return $colorMap[$name];
        }

        // 2. Check partial match in color map
        foreach ($colorMap as $key => $color) {
            if (stripos($name, (string) $key) !== false) {
                return $color;
            }
        }

        // 3. Check role-based color override (only for Pokjas)
        if ($role && isset(self::ROLE_COLORS[$role])) {
            // Apply slight darkening/lightening based on index for multiple series if needed, 
            // but for now return base role color.
            return self::ROLE_COLORS[$role];
        }

        // 4. Fallback to default grayscale palette
        return self::BAR_COLORS[$index % count(self::BAR_COLORS)];
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

    private function drawLegend(array $seriesNames, int $height, array $colorMap = [], ?string $role = null): string
    {
        $svg = '<g transform="translate(' . self::MARGIN_LEFT . ',' . (self::MARGIN_TOP + $height + 60) . ')">';
        
        $xOffset = 0;
        foreach ($seriesNames as $idx => $name) {
            $color = $this->resolveColor($name, $idx, $colorMap, $role);
            
            $svg .= '<rect x="' . $xOffset . '" y="0" width="12" height="12" fill="' . $color . '" stroke="#000000" stroke-width="1" />';
            $svg .= '<text x="' . ($xOffset + 18) . '" y="10" font-size="11" fill="#000000">' . htmlspecialchars($name) . '</text>';
            
            // Approximate width for next item
            $xOffset += (strlen($name) * 7) + 40;
        }
        
        $svg .= '</g>';
        return $svg;
    }
}
