<?php


namespace wpgp
{

    class SearchResult
    {

        public bool $success;
        public string $type;
        public Point $center;
        public array $locations;

        const ADMIN_AREA_1 = 'adminArea1';
        const PROXIMITY = 'proximity';
        const TYPES = [
            self::ADMIN_AREA_1,
            self::PROXIMITY
        ];

        public function __construct(bool $success, string $type, Point $center, array $locations)
        {
            $this->success = $success;
            $this->type = $type;
            if (in_array($type, self::TYPES, true) === false) {
                $types = implode(', ', self::TYPES);
                error_log(
                    "wpgp\\SearchResult: $type is not valid. Must be one of $types"
                );
            }
            $this->center = $center;
            $this->locations = $locations;
        }

    }
}