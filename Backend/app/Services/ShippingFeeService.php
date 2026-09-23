<?php

namespace App\Services;

class ShippingFeeService
{
    public const METHOD_STANDARD = 'standard';
    public const METHOD_EXPRESS = 'express';

    public function calculate(float $distanceKm, string $method = self::METHOD_STANDARD): float
    {
        $baseFee = 15000;
        $feePerKm = 5000;

        $fee = $baseFee + (ceil($distanceKm) * $feePerKm);

        if ($method === self::METHOD_EXPRESS) {
            $fee *= 2;
        }

        return round($fee, 2);
    }

    public function fees(float $distanceKm): array
    {
        return [
            self::METHOD_STANDARD => $this->calculate($distanceKm, self::METHOD_STANDARD),
            self::METHOD_EXPRESS => $this->calculate($distanceKm, self::METHOD_EXPRESS),
        ];
    }
}
