<?php


namespace wpgp
{

    class SearchResult
    {

        public bool $success;
        public string $type;
        public Point $center;
        public array $locations;

        const ALL = 'ALL';
        const REGION = 'REGION';
        const COUNTRY = 'COUNTRY';
        const PREMISE = 'PREMISE';
        const LOCALITY = 'LOCALITY';
        const TYPES = [
            self::ALL,
            self::REGION,
            self::COUNTRY,
            self::PREMISE,
            self::LOCALITY
        ];

        public function __construct(bool $success, string $type, Point $center, array $locations)
        {
            $this->success = $success;
            $this->type = $type;
            if (in_array($type, self::TYPES, true) === false) {
                $types = self::TYPES;
                error_log(
                    "wpgp\\SearchResult: $type is not valid. Must be one of $types"
                );
            }
            $this->center = $center;
            $this->locations = $locations;
        }

    }
}