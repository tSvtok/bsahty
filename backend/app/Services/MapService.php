<?php

namespace App\Services;

use App\Models\Spot;

class MapService
{
    /**
     * Get spots near a specific coordinate within a radius.
     * 
     * @param float $lat
     * @param float $lng
     * @param int $radiusInMeters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNearbySpots(float $lat, float $lng, int $radiusInMeters = 5000)
    {
        // For now, return all approved spots since we're using JSON coordinates
        // In production, you'd want proper geospatial queries
        return Spot::where('status', 'APPROVED')->get();
    }

    /**
     * Example placeholder for future external Geocoding (Address to Lat/Lng).
     */
    public function geocodeAddress(string $address)
    {
        // Integration with Google Maps API or OpenStreetMap Nominatim would go here.
        return [
            'lat' => 0.0,
            'lng' => 0.0,
        ];
    }
}
