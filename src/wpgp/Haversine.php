<?php


namespace wpgp
{
    class Haversine
    {

        const EARTH_RADIUS_KM = 6378.1370;
        
        // from x to y distance
        public static function getDistance(Point $x, Point $y) : Distance
        {
            // convert from degress to radians
            $xLatRadians = deg2rad($x->lat);
            $xLngRadians = deg2rad($x->lng);
            $yLatRadians = deg2rad($y->lat);
            $yLngRadians = deg2rad($y->lng);

            // compute the difference of lat/lng
            $latDelta = $yLatRadians - $xLatRadians;
            $lngDelta = $yLngRadians - $xLngRadians;

            // compute the angle 
            $angle = 2 * asin(
                sqrt(
                    pow(sin($latDelta / 2), 2) +
                    cos($xLatRadians) * 
                    cos($yLatRadians) * 
                    pow(sin($lngDelta / 2), 2)
                )
            );
            
            // compute the kilometers as a function of earth's radius
            $kilometers = $angle * self::EARTH_RADIUS_KM;
            return new Distance($kilometers);
        }
    }
}