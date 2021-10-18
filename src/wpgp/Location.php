<?php


namespace wpgp 
{
    class Location
    {
        public int $postId;
        
        public string $name;
        public string $phone;
        public array $openingHours;
        public array $weekdayText;

        public string $formattedAddress;
        public string $streetNumber;
        public string $locality;
        public string $adminArea2;
        public string $adminArea1;
        public string $country;
        public string $postalCode;
        
        public float $rating;
        public array $reviews;
        public array $reviewCount;
        
        public string $mapsUrl;
        public float $lat;
        public float $lng;
        public int $utcOffset;
        public string $vicinity;
        public string $website;        
        
        private string $_placeId;
        
        public function __construct(int $postId = null)
        {
            if (is_null($postId) === true) {
                $this->postId = get_the_ID();
            }
            $this->postId = $postId;
            $this->_placeId = MetaBox::getMetaItem(
                $this->postId, 
                MetaBox::PLACE_ID_KEY
            );
            $this->name = null;
            $this->phone = null;
            $this->openingHours = null;
            $this->weekdayText = null;
            $this->formattedAddress = null;
            $this->streetNumber = null;
            $this->locality = null;
            $this->adminArea2 = null;
            $this->adminArea1 = null;
            $this->country = null;
            $this->postalCode = null;
            $this->rating = null;
            $this->reviews = null;
            $this->reviewCount = null;
            $this->mapsUrl = null;
            $this->lat = null;
            $this->lng = null;
            $this->utcOffset = null;
            $this->vicinity = null;
            $this->website = null;
            $this->initialize();
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
        }

        private function parse(string $data) : void
        {
            // if data is still empty, do nothing
            if (empty($data) === true) {
                error_log('wpgp\\Location: Could not get GooglePlaceDetail.');
                return;
            }

            $data = json_decode($data, true);
            if (is_null($data) === true) {
                // TODO: log response
                error_log('wpgp\\Location: Could not parse GooglePlaceDetail.');
                return;
            }
            if ($data['status'] !== 'OK') {
                // TODO: log response
                error_log('wpgp\\Location: Return from GooglePlaceDetail not OK');
                return;
            }
            $data = $data['result'];

            // parse the google place detail data into attributes
            $this->name = $data['name'] ?? '';
            $this->phone = $data['formatted_phone_number'] ?? '';
            $this->openingHours = $data['opening_hours'] ?? '';
            $this->weekdayText = $data['weekday_text'] ?? '';
            
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

            $this->rating = $data['rating'] ?? '';
            $this->reviews = $data['reviews'] ?? '';
            $this->reviewCount = $data['user_ratings_total'] ?? '';

            $this->mapsUrl = $data['url'] ?? '';
            $this->lat = $data['geometry']['location']['lat'] ?? '';
            $this->lng = $data['geometry']['location']['lng'] ?? '';
            $this->utcOffset = $data['utc_offset'] ?? '';
            $this->vicinity = $data['vicinity'] ?? '';
            $this->website = $data['website'] ?? '';

        }
    }
}