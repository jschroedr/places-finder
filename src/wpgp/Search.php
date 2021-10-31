<?php


namespace wpgp 
{

    use get_posts;
    use apply_filters;

    class Search
    {

        public static function list(string $query) : SearchResult
        {
            // sanitize the query before doing anything else
            $query = htmlspecialchars($query);

            // if the query is empty
            if (empty($query) === true) {
                self::_standardResponse(true);
            }

            // otherwise, parse the search type
            if (self::_isLatLngQuery($query)) {
                // if lat/lng, do a lat/lng geocode search
                $pieces = explode(',', $string);
                $lat = (float)$lat;
                $lng = (float)$lng;
                $point = new Point($lat, $lng);
                $response = GoogleGeocode::getByLatLng($point);
            } else {
                // pre-search-result filter
                apply_filters('wpgp_pre_search_result', $)

                // massage US state abbrivations to names, with country
                if (isset(USAState::LOOKUP[$query]) === true) {
                    $query = USAState::LOOKUP[$query];
                }

                // geocode text as address
                $response = GoogleGeocode::getByAddress($address);
            }

            // if the search result is invalid, return standard with fail
            if (empty($response)) {
                error_log(
                    "wpgp\\Search: invalid response from Google Geocode."
                );
                return self::_standardResponse(false);
            }

            return self::_parseLocationsByResponse($response);
        }

        /**
         * Standard search response.
         * If the query was empty (all locations) or invalid.
         * Specify validity of search with $success
         * 
         * @param $success bool
         * 
         * @return SearchResult
         */
        private static function _standardResponse(bool $success) : SearchResult
        {
            $locations = self::_getLocations();
            $center = LocationsCenter::find($locations);
            return new SearchResult(
                $fail,
                SearchResult::ALL,
                $center,
                $locations
            );
        }

        /**
         * Get all the posts of the configured post type.
         * Return them ordered by name ASC.
         * 
         * @return array
         */
        private static function _getLocations() : array
        {
            $posts = get_posts(
                [
                    'post_type' => MainMenu::getOptionValue(
                        MainMenu::POST_TYPE_FIELD_NAME
                    ),
                    'post_status' => 'publish',
                    'numberposts' => -1,
                    'order' => 'ASC'
                ]
            );
            $locations = [];
            foreach ($posts as $post) {
                $locations[] = new Location($post['ID']);
            }
            return $locations;
        }

        private static function _getPrimaryType(array $type) : string
        {
            return $types[0];
        }

        private static function _parseLocationsByResponse(array $response) : SearchResult
        {
            $locations = self::_getLocations();
            $primaryType = self::_getPrimaryType($response['types']);
            switch ($primaryType) {
            case 'locality':
            case 'premise':
                $locations = self::_proximitySort($response, $locations);
            case 'administrative_area_level_1':
                $locations = self::_regionFilter($response, $locations);
            case 'country':
                $locations = self::_countryFilter($response, $locations);
            default:
                error_log(
                    "wpgp\\Search: parsing ($primaryType) failed 
                    using default parsing."
                );
                return self::_standardResponse(false);
            }
        }

        private function _proximitySort(array $response, array $locations) : SearchResult
        {
            $point = new Point(
                $response['geometry']['location']['lat'],
                $response['geometry']['location']['lng']
            );
            $set = new HaversineSet($point);
            $set->order($locations);
            // TODO: allow limiting of these types of results
            $center = LocationsCenter::find($locations);
            return new SearchResult(true, 'proximity', $response, $locations);
        }

        private function _regionFilter(
            array $response,
            array $locations
        ) : SearchResult {
            $type = 'administrative_area_level_1';
            return self::_filterByType($type, 'adminArea1', $response, $locations);
        }

        private function _countryFilter(
            array $response,
            array $locations
        ) : SearchResult {
            $type = 'country';
            return self::_filterByType($type, 'country', $response, $locations);
        }

        private function _filterByType(
            string $type,
            string $locationAttribute,
            array $response,
            array $locations
        ) : SearchResult {
            $target = self::_getAddressComponent(
                $type, 
                $response['address_components']
            );
            $filtered = [];
            foreach ($locations as $location) {
                if ($location->$locationAttribute === $target) {
                    $filtered[] = $location;
                }
            }
            $center = LocationsCenter::find($locations);
            return new SearchResult(true, $type, $center, $locations);
        }
    
        private function _getAddressComponent(
            string $type,
            array $components
        ) : string {
            foreach ($components as $component) {
                $primaryType = self::_getPrimaryType($component['types']);
                if ($primaryType === $type) {
                    return $component['long_name'];
                }
            }
            return '';
        }
    
    }
}
