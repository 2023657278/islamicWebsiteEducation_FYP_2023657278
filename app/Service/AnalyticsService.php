<?php

namespace App\Services;

class AnalyticsService
{
    /**
     * Calculate Linear Regression Slope (m) based on Chapter 3.6.2 Accuracy Testing.
     * Formula: m = (N(Σxy) - (Σx)(Σy)) / (N(Σx^2) - (Σx)^2)
     * Reference: Report Page 61
     * * @param array $scores Array of quiz scores (e.g., [50, 56, 60, 62.5])
     * @return float The slope value (m) rounded to 2 decimal places
     */
    public function calculateSlope($scores)
    {
        $n = count($scores); // N: Total number of quiz attempts
        
        // We need at least 2 data points to calculate a trend line
        if ($n < 2) {
            return 0; 
        }

        $sumX = 0;  // Σx (Sum of Quiz Attempts: 1, 2, 3...)
        $sumY = 0;  // Σy (Sum of Scores)
        $sumXY = 0; // Σxy (Sum of Attempt * Score)
        $sumXX = 0; // Σx^2 (Sum of Attempt Squared)

        // Loop through all scores to calculate the Sums
        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;       // Attempt number (1, 2, 3...)
            $y = $scores[$i];  // The score percentage

            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumXX += ($x * $x);
        }

        // Apply the Least Squares Formula from Page 61
        $numerator = ($n * $sumXY) - ($sumX * $sumY);
        $denominator = ($n * $sumXX) - ($sumX * $sumX);

        // Prevent division by zero error
        if ($denominator == 0) {
            return 0;
        }

        $slope = $numerator / $denominator;

        // Round to 2 decimal places as shown in Table 3.27
        return round($slope, 2);
    }

    /**
     * Translate the slope into a Status based on Figure 3.10 Flowchart.
     * Reference: Report Page 47
     * * @param float $slope
     * @return string Status (Excellent, Improving, Stable, Warning, Critical)
     */
    public function getInterpretation($slope)
    {
        // Logic strictly from Figure 3.10 (Page 47)
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