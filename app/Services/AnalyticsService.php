<?php

namespace App\Services;

class AnalyticsService
{
    /**
     * Calculate Linear Regression Slope (m)
     * Formula: m = (N(Σxy) - (Σx)(Σy)) / (N(Σx^2) - (Σx)^2)
     */
    public function calculateSlope($scores)
    {
        $n = count($scores);
        
        // Need at least 2 data points for a trend
        if ($n < 2) {
            return 0; 
        }

        $sumX = 0;  // Attempt Number
        $sumY = 0;  // Score
        $sumXY = 0; // Attempt * Score
        $sumXX = 0; // Attempt^2

        // Reset array keys to ensure 0,1,2 index for calculations
        $scores = array_values($scores);

        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;       // Attempt 1, 2, 3...
            $y = $scores[$i];  // Score 50, 60...

            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumXX += ($x * $x);
        }

        $numerator = ($n * $sumXY) - ($sumX * $sumY);
        $denominator = ($n * $sumXX) - ($sumX * $sumX);

        if ($denominator == 0) {
            return 0;
        }

        return round($numerator / $denominator, 2);
    }

    /**
     * Interpret the slope into a readable status
     */
    public function getInterpretation($slope)
    {
        if ($slope >= 3.0) {
            return "Excellent";
        } elseif ($slope >= 1.0) {
            return "Improving";
        } elseif ($slope >= -1.0) {
            return "Stable";
        } elseif ($slope >= -3.0) {
            return "Warning";
        } else {
            return "Critical";
        }
    }
}