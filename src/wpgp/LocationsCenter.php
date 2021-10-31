<?php


namespace wpgp
{

    class LocationsCenter
    {
    
        public static function find(
            array $locations
        ) : Point
        {

            // generate pi for points
            $pi = pi();

            // placeholders for cartesian points
            $x = 0;
            $y = 0;
            $z = 0;

            // keep track of how many coordinates we run through
            $count = 0;

            foreach($locations as $location) 
            {
                $point = $location->point;
                $lat = self::normalizePoint($point->lat, $pi);
                $lng = self::normalizePoint($point->lng, $pi);

                // increment cartesian coordinates
                $x += self::getA($lat, $lng);
                $y += self::getB($lat, $lng);
                $z += self::getC($lat);
            
                $count ++;
            }

            // divide by number of points processed
            $x /= $count;
            $y /= $count;
            $z /= $count;

            $lng = atan2($y, $x);
            $hyp = sqrt(($x * $x) + ($y * $y));
            $lat = atan2($z, $hyp);

            $lat = $lat * 180 / $pi;
            $lng = $lng * 180 / $pi;

            return new Point($lat, $lng);
        }
 
        private static function normalizePoint(float $point, float $pi) : float 
        {
            return $point * $pi / 180;
        }

        private static function getA(float $lat, float $lng) : float 
        {
            return cos($lat) * cos($lng);
        }

        private static function getB(float $lat, float $lng) : float 
        {
            return cos($lat) * sin($lng);
        }

        private static function getC(float $lat) : float
        {
            return sin($lat);
        }

    }
}