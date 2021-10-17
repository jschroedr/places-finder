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

        const URL = 'https://maps.googleapis.com/maps/api/place/details/json';

        private function refresh() : CacheItem
        {
            return new CacheItem();
        }

        private function parse(CacheItem $cached) 
        {
            return;
        }

        public function get() : array
        {
            // check the data cache
            $key = 'place-details-' . $this->placeId;
            $cached = Cache::get($key);
            // make a request if nothing or too old
            if ($cached->expired) {
                $cached = $this->refresh();
                // cache the response
                Cache::set($key, $cached);
            }
            // set specialized attributes
            $this->parse($cached);

            // return the raw data json
            return $cached->raw;
        }
    }
}