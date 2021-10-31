<?php


namespace wpgp 
{
    class Location
    {
        public int $postId;
        
        public bool $initialized = false;
        public string $name = '';
        public string $phone = '';
        public array $openingHours = [];
        public array $weekdayText = [];

        public string $formattedAddress = '';
        public string $streetNumber = '';
        public string $locality = '';
        public string $adminArea2 = '';
        public string $adminArea1 = '';
        public string $country = '';
        public string $postalCode = '';
        
        const INVALID_RATING = -1.0;
        const INVALID_REVIEW_COUNT = -1;
        public float $rating;
        public array $reviews = [];
        public int $reviewCount;
        
        public string $mapsUrl = '';
        public float $lat;
        public float $lng;
        
        public Point $point;
        public Distance $distance = 0.0;
        
        public int $utcOffset = -1;
        public string $vicinity = '';
        public string $website = '';  
        
        private string $_placeId;
        
        const INVALID_COORD = -181.0;

        public function __construct(int $postId = null)
        {
            $this->rating = $this::INVALID_RATING;
            $this->reviewCount = $this::INVALID_REVIEW_COUNT;
            $this->lat = $this::INVALID_COORD;
            $this->lng = $this::INVALID_COORD;

            if (is_null($postId) === true) {
                $this->postId = get_the_ID();
            }
            $this->postId = $postId;
            $this->_placeId = MetaBox::getMetaItem(
                $this->postId, 
                MetaBox::PLACE_ID_KEY
            );

            $this->initialized = false;
            $this->initialize();
            $this->point = new Point($this->lat, $this->lng);
        }

        private function initialize() : void
        {
            // do nothing if place id is empty
            if (empty($this->_placeId) === true) {
                return;
            }
            // try to get cached data first
            $data = GooglePlaceDetail::getCached($this->_placeId);
            if (empty($data) === true) {
                $data = GooglePlaceDetail::get($this->_placeId);
            }
            $this->parse($data);
            $this->initialized = true;
        }

        private function parse(array $data) : void
        {
            // if data is still empty, do nothing
            if (empty($data) === true) {
                error_log('wpgp\\Location: Could not get GooglePlaceDetail.');
                return;
            }

            // parse the google place detail data into attributes
            $this->name = $data['name'] ?? '';
            $this->phone = $data['formatted_phone_number'] ?? '';
            $this->openingHours = $data['opening_hours'] ?? [];
            $this->weekdayText = $data['weekday_text'] ?? [];
            
            $this->formattedAddress = $data['formatted_address'] ?? '';
            foreach ($data['address_components'] as $component) {
                $type = $component['types'][0];
                $value = $component['long_name'];
                switch ($type) {
                case 'street_number':
                    $this->streetNumber = $value;
                    break;
                case 'locality':
                    $this->locality = $value;
                    break;
                case 'administrative_area_level_2':
                    $this->adminArea2 = $value;
                    break;
                case 'administrative_area_level_1':
                    $this->adminArea1 = $value;
                    break;
                case 'country':
                    $this->country = $value;
                    break;
                case 'postal_code':
                    $this->postalCode = $value;
                    break;
                default:
                    break;
                }
            }

            $this->rating = $data['rating'] ?? 0.0;
            $this->reviews = $data['reviews'] ?? [];
            $this->reviewCount = $data['user_ratings_total'] ?? 0;

            $this->mapsUrl = $data['url'] ?? '';
            $this->lat = $data['geometry']['location']['lat'] ?? self::INVALID_COORD;
            $this->lng = $data['geometry']['location']['lng'] ?? self::INVALID_COORD;
            $this->utcOffset = $data['utc_offset'] ?? '';
            $this->vicinity = $data['vicinity'] ?? '';
            $this->website = $data['website'] ?? '';

        }
    }
}