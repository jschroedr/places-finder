<?php


namespace wpgp
{
    class HaversineSet 
    {

        public Point $resultFrom;

        public function __construct(Point $point)
        {
            $this->resultFrom = $point;
        }

        // sorts an array of Result objects by their distance to the geocode result
        public function order(array &$rows)
        {
            $result = usort($rows, [$this, 'sort']);
            if ($result === false) {
                error_log('wpgp\\HaversineSet: usort() failed.');
            }
        }

        // sorts listings with distance first, by distance in km
        // sorts listings without distance second, by title
        private function sort(Location $a, Location $b) 
        {
            // return 0 (same) if we are given two locations
            // with the same post id
            if ($a->postId === $b->postId) {
                return 0;
            }
            $aIsNull = is_null($a->point);
            $bIsNull = is_null($b->point);
            if ($aIsNull === true && $bIsNull === false) {
                // a does not have point information
                //
                // a is greater than b
                // (a will be de-priotizied and sorted by name)
                return 1;
            } elseif ($bIsNull === true && $aIsNull === false) {
                // b does not have point information
                // 
                // a is less than b (b will be sorted by name)
                return -1;
            } elseif ($bIsNull === true && $aIsNull === true) {
                // string comparison on alphabetic order
                $result = strcmp($a->name, $b->name);
                if ($result === 0) {
                    return 0;
                } elseif ($result > 0) {
                    return 1;
                } else {
                    return -1;
                }
            } 

            // don't calculate distances twice
            if (is_null($a->distance)) {
                $a->distance = Haversine::getDistance($this->resultFrom, $a->point);
            }
            if (is_null($b->distance)) {
                $b->distance = Haversine::getDistance($this->resultFrom, $b->point);
            }
            
            /*
            The comparison function must return an integer 
            less than, equal to, or greater than zero
            if the first argument is considered to be 
            respectively less than, equal to, or greater than the second.
            
            https://www.php.net/manual/en/function.usort.php
            */
            $aKM = $a->distance->getKilometers();
            $bKM = $b->distance->getKilometers();
            if ($aKM === $bKM) {
                return 0;
            } else {
                return ($aKM < $bKM) ? -1 : 1;
            }
        }

    }
}