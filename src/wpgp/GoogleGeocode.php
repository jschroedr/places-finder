<?php


namespace wpgp
{

    class GoogleGeocode extends GoogleApi
    {

        const LANGUAGE = 'en';
        const URL = 'https://maps.googleapis.com/maps/api/geocode/json';

        private static function getMock(string $address, array $mockConfig) : array
        {
            $status = $mockConfig['status'] ?? '200';
            $filename = "geocode-$address-$status.json";
            $response = MockHelper::getResponseContent($filename);
            return json_decode($response, true);
        }

        public static function get(string $address, array $mockConfig = []) : array
        {
            $address = htmlspecialchars($address);
            $key = self::getApiKey();
            $url = self::URL . '?' . "address=$address&$key";

            // TODO: MOCKING SUPPORT
            if (Configuration::isTest() === false) {
                $response = self::getProduction($url);
            } else {
                $response = self::getMock($address, $mockConfig);
            }

            if (self::checkApiResponse($response) === false) {
                return [];
            }

            // extract the result, but DO NOT use the cache
            return self::setAndReturn($response);
        }
    }
}