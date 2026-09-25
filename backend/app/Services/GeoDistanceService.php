<?php

namespace App\Services;

class GeoDistanceService
{
    public function distanceInKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadiusKm = 6371;

        $latDiff = deg2rad($toLat - $fromLat);
        $lngDiff = deg2rad($toLng - $fromLng);

        $fromLat = deg2rad($fromLat);
        $toLat = deg2rad($toLat);

        $a = sin($latDiff / 2) ** 2
            + cos($fromLat) * cos($toLat) * sin($lngDiff / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 2);
    }
}
