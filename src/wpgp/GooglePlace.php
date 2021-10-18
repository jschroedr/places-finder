<?php

namespace wpgp
{
    class GooglePlace
    {
        public string $placeId;

        public function __construct(string $placeId)
        {
            $this->placeId = $placeId;
        }

        public function get() : string
        {
            // check the data cache
            $key = $this->getKey();
            $data = Cache::get($key);

            // make a request if not found or too old
            if (empty($data) === true) {
                $data = $this->refresh();
                // cache the response
                Cache::set($key, $data);
            }
            // set specialized attributes
            $this->parse($data);

            // return the raw data json
            return $data;
        }

        private function getKey() : string
        {
            return 'place-details-' . $this->placeId;
        }

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

        private function refresh() : string
        {
            $params = [
                'place_id' => $this->placeId,
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
                error_log('wpgp\\GooglePlace:')
            }
            return '';
        }

        private function parse(string $cached) : void
        {
            return;
        }

    }
}