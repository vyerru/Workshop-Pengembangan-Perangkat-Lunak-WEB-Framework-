<?php

namespace App\Services;

class GeolocationService
{
    const EARTH_RADIUS = 6371000;

    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $lng1Rad = deg2rad($lng1);
        $lng2Rad = deg2rad($lng2);

        $dlat = $lat2Rad - $lat1Rad;
        $dlng = $lng2Rad - $lng1Rad;

        $a = sin($dlat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($dlng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS * $c;
    }

    public function hitungThresholdEfektif(float $maxJarak, float $accToko, float $accSales): float
    {
        return $maxJarak + $accToko + $accSales;
    }

    public function isValid(float $jarak, float $threshold): bool
    {
        return $jarak <= $threshold;
    }
}
