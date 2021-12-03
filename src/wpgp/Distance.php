<?php


namespace wpgp
{
    class Distance
    {
        private float $_kilometers;

        const KM_TO_MILES = 0.6214;

        public function __construct(float $kilometers)
        {
            $this->_kilometers = $kilometers;
            
        }
        
        public function getKilometers() : float
        {
            return $this->_kilometers;
        }

        public function getMiles() : float
        {
            return $this->_kilometers * self::KM_TO_MILES;
        }
    }
}