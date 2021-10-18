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
        
        public static function getCached(string $placeId) : string 
        {
            return Cache::get(self::CACHE_KEY, $placeId);
        }

        public static function get(string $placeId) : string
        {
            // TODO: get and set api key for request
            $params = [
                'place_id' => $placeId,
                'fields' => implode(',', self::BASIC_FIELDS),
                'language' => self::LANGUAGE
            ];
            $params = http_build_query($params);
            $response = wp_remote_get(
                self::URL . $params,
                [
                    'method' => 'GET'
                ]
            );
            if (is_wp_error($response)) {
                error_log(
                    'wpgp\\GooglePlaceDetail: Request failed ' . 
                    $response->get_error_message()
                );
                return '';
            }
            return wp_remote_retrieve_body($response);
        }

    }
}