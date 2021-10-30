<?php

namespace wpgp
{

    class GoogleApi
    {
        public static function getApiKey() : string
        {
            return MainMenu::getOptionValue(
                MainMenu::SERVER_API_KEY_FIELD_NAME
            );
        }

        public static function getProduction(string $url) : array
        {
            $response = wp_remote_get(
                $url,
                [
                    'method' => 'GET'
                ]
            );
            if (is_wp_error($response)) {
                $className = self::class;
                error_log(
                    "$className: Request failed " . 
                    $response->get_error_message()
                );
                return [];
            }
            $response = wp_remote_retrieve_body($response);
            return json_decode($response, true);
        }

        public static function checkApiResponse(array $response) : bool
        {
            $className = self::class;
            if (empty($response) === true) {
                return $response;
            }
            if (isset($response['error_message'])) {
                error_log(
                    "$className: Request failed " . 
                    json_encode($response)
                );
                return [];
            }
            if ($response['status'] !== 'OK') {
                error_log(
                    "$className: Problem getting place details " . 
                    json_encode($response)
                );
            }
        }

        public static function setAndReturn(array $response, string $placeId = '', string $cacheKey = '') : array
        {
            $response = $response['result'];
            if (empty($cacheKey) === false) {
                Cache::set($cacheKey, json_encode($response), $placeId);
            }
            return $response;
        }
    }
}