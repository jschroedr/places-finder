<?php

namespace wpgp
{
    class GooglePlaceDetail
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

        public static function get(string $placeId) : array
        {
            // TODO: get and set api key for request
            $params = [
                'place_id' => $placeId,
                'fields' => implode(',', self::BASIC_FIELDS),
                'language' => self::LANGUAGE,
                'key' => MainMenu::getOptionValue(
                    MainMenu::SERVER_API_KEY_FIELD_NAME
                )
            ];
            $params = http_build_query($params);
            $url = self::URL . '?' . $params;
            $response = wp_remote_get(
                $url,
                [
                    'method' => 'GET'
                ]
            );
            if (is_wp_error($response)) {
                error_log(
                    'wpgp\\GooglePlaceDetail: Request failed ' . 
                    $response->get_error_message()
                );
                return [];
            }
            $response = wp_remote_retrieve_body($response);
            $response = json_decode($response, true);
            if (isset($response['error_message'])) {
                error_log(
                    'wpgp\\GooglePlaceDetail: Request failed ' . 
                    $response['error_message']
                );
                return [];
            }
            $response = $response['result'];
            Cache::set(self::CACHE_KEY, json_encode($response), $placeId);;
            return $response;
        }

    }
}