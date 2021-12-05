<?php


namespace wpgp
{

    class GoogleGeocode extends GoogleApi
    {

        const LANGUAGE = 'en';
        const URL = 'https://maps.googleapis.com/maps/api/geocode/json';

        private static function _getByAddressMock(string $address) : array
        {
            $filename = "geocode-$address.json";
            $response = MockHelper::getResponseContent($filename);
            return json_decode($response, true);
        }

        public static function getByAddress(string $address) : array
        {
            $address = htmlspecialchars($address);
            $key = self::getApiKey();
            $url = self::URL . '?' . "address=$address&$key";

            // TODO: MOCKING SUPPORT
            if (Configuration::isTest() === false) {
                $response = self::getProduction($url);
            } else {
                $response = self::_getByAddressMock($address);
            }

            if (self::checkApiResponse($response) === false) {
                return [];
            }

            // extract the result, but DO NOT use the cache
            return self::setAndReturn($response);
        }

        private static function _getByLatLngMock(string $latlng) : array
        {
            $filename = "geocode-$latlng.json";
            $response = MockHelper::getResponseContent($filename);
            return json_decode($response, true);
        }

        public static function getByLatLng(Point $point) : array
        {
            $key = self::getApiKey();
            $lat = $point->lat;
            $lng = $point->lng;
            $latlng = "$lat,$lng";
            $url = self::URL . '?' . "latlng=$latlng=&key=$key";

            if (Configuration::isTest() === false) {
                $response = self::getProduction($url);
            } else {
                $response = self::_getByLatLngMock($latlng);
            }

            if (self::checkApiResponse($response) === false) {
                return [];
            }

            // extract the result, but DO NOT use the cache
            return $response['result'];
        }
    }
}