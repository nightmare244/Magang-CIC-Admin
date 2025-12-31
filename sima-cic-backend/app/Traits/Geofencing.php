<?php

namespace App\Traits;

trait Geofencing
{
    /**
     * Menghitung jarak antara dua titik di bumi (Haversine formula).
     * Mengembalikan jarak dalam METER.
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @return float Jarak dalam METER
     */
    public function hitungJarak(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $bumiRadius = 6371000; // Radius bumi dalam METER

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $bumiRadius;
    }
}
// (Tidak ada komentar referensi di luar tag PHP lagi)
