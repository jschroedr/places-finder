<?php


namespace wpgp
{

    class Point {

        public float $lat;
        public float $lng;

        public function __construct(float $lat, float $lng) 
        {
            $this->lat = $lat;
            $this->lng = $lng;
        }

        private function checkCoord(float $coord) : bool
        {
            return (-180 <= $coord) && ($coord <= 180);
        }

        public function validate() : bool
        {
            return $this->checkCoord($this->lat) && $this->checkCoord($this->lng);
        }

    }

}