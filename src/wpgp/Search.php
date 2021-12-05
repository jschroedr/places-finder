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

            // if valid coords are detected, do a lat/lng geocode search
            $coords = self::_isLatLngQuery($query);
            if (empty($coords) === false) {
                $lat = (float)$coords[0];
                $lng = (float)$coords[1];
                $point = new Point($lat, $lng);
                $response = GoogleGeocode::getByLatLng($point);
            } else {
                // perform an address-based search

                // pre-search-result filter
                // apply_filters('wpgp_pre_search_result', )

                // massage US state abbreviations to names, with country
                if (isset(USAState::LOOKUP[$query]) === true) {
                    $query = USAState::LOOKUP[$query];
                }

                // geocode text as address
                $response = GoogleGeocode::getByAddress($query);
            }

            // if the search result is invalid, return standard with fail
            if (empty($response)) {
                error_log(
                    "wpgp\\Search: invalid response from Google Geocode."
                );
                return self::_standardResponse(false);
            }

            // always take the first (closest) response from the geocode api
            $response = $response[0];

            return self::_parseLocationsByResponse($response);
        }

        /**
         * Parses a user's query to see if valid coordinates have been provided
         * 
         * Returns a valid set of [lat, lng] coordinates upon success, or empty
         * array on failure.
         * 
         * @param string $query the user's search query
         * 
         * @return array
         */
        private static function _isLatLngQuery(string $query) : array
        {
            $limit = 2;
            $coords = explode(',', $query, $limit);
            if (count($coords) !== $limit) {
                return [];
            } else {
                foreach ($coords as &$coord) {
                    // remove any whitespace before anything else
                    $coord = trim($coord);
                    if (is_numeric($coord) === false) {
                        return [];
                    }
                    $coord = (float)$coord;
                    // coordinates must be between -180 and 180
                    if ((-180 < $coord && $coord < 180) === false) {
                        return [];    
                    }
                }
                return $coords;
            }
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
                $success,
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
            $postType = MainMenu::getOptionValue(
                MainMenu::POST_TYPE_FIELD_NAME
            );
            $postType = empty($postType) === true ? 'post' : $postType;
            $posts = get_posts(
                [
                    'post_type' => $postType,
                    'post_status' => 'publish',
                    'numberposts' => -1,
                    'order' => 'ASC'
                ]
            );
            $locations = [];
            // we don't have time in Search to pay for the API
            // time cost for each location
            // use the cache only
            $cacheOnly = true;
            foreach ($posts as $post) {
                $locations[] = new Location($post->ID, $cacheOnly);
            }
            return $locations;
        }

        private static function _getPrimaryType(array $type) : string
        {
            return $type[0];
        }

        private static function _parseLocationsByResponse(array $response) : SearchResult
        {
            $locations = self::_getLocations();
            // if there are no locations, just return the standard successful response
            if (empty($locations) === true) {
                return self::_standardResponse(true);
            }
            $primaryType = self::_getPrimaryType($response['types']);
            switch ($primaryType) {
            case 'administrative_area_level_1':
                return self::_regionFilter($response, $locations);
                break;
            default:
                return self::_proximitySort($response, $locations);
                break;
            }
        }

        private static function _proximitySort(
            array $response, 
            array $locations
        ) : SearchResult {
            $point = new Point(
                $response['geometry']['location']['lat'],
                $response['geometry']['location']['lng']
            );
            $set = new HaversineSet($point);
            $set->order($locations);
            $center = LocationsCenter::find($locations);
            return new SearchResult(true, 'proximity', $center, $locations);
        }

        private static function _regionFilter(
            array $response, 
            array $locations
        ) : SearchResult {
            $type = 'administrative_area_level_1';
            return self::_filterByType($type, 'adminArea1', $response, $locations);
        }

        private static function _filterByType(
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
    
        private static function _getAddressComponent(
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
