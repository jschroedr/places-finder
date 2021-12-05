<?php

namespace wpgp
{

    use wpgp\Configuration;

    class GooglePlaceDetail extends GoogleApi
    {

        const CACHE_KEY = 'google-place-detail';

        const LANGUAGE = 'en';

        const URL = 'https://maps.googleapis.com/maps/api/place/details/json';

        const BASIC_FIELDS = [
            'address_component',
            'adr_address', 
            'business_status', 
            'formatted_address',
            'geometry', 
            'icon', 
            'icon_mask_base_uri', 
            'icon_background_color', 
            'name', 
            'photo', 
            'place_id',
            'plus_code',
            'type', 
            'url', 
            'utc_offset', 
            'vicinity'
        ];
        
        public static function getCached(string $placeId) : array 
        {
            $response = Cache::get(self::CACHE_KEY, $placeId);
            if (empty($response) === true) {
                return [];
            }
            return json_decode($response, true);
        }

        private static function getMock(string $placeId) : array
        {
            $filename = "google-place-detail-$placeId.json";
            $response = MockHelper::getResponseContent($filename);
            return json_decode($response, true);
        }

        public static function get(string $placeId) : array
        {
            $params = [
                'place_id' => $placeId,
                'fields' => implode(',', self::BASIC_FIELDS),
                'language' => self::LANGUAGE,
                'key' => self::getApiKey()
            ];
            $params = http_build_query($params);
            $url = self::URL . '?' . $params;

            if (Configuration::isTest() === false) {
                $response = self::getProduction($url);
            } else {
                $response = self::getMock($placeId);
            }
            if (self::checkApiResponse($response) === false) {
                return [];
            }
            
            // we may only cache the lat/lng and place id in accord with
            // the maps service agreement as stated in the google places 
            // service agreement (3.2.3(b))
            // https://cloud.google.com/maps-platform/terms#3.-license.
            // https://cloud.google.com/maps-platform/terms/maps-service-terms
            $data = [
                'lat' => $response['result']['geometry']['location']['lat'],
                'lng' => $response['result']['geometry']['location']['lng'],
                'place_id' => $response['result']['place_id']
            ];
            self::setCache($data, $placeId, self::CACHE_KEY);
            
            return $response['result'];
        }

    }
}