<?php


namespace wpgp
{

    class Configuration
    {

        public static function isTest() : bool
        {
            $testFlag = $_ENV['WPGP_TESTING'] ?? '';
            return !empty($testFlag);
        }
    }

}