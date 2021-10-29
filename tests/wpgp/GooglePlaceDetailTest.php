<?php

namespace wpgp
{

    use PHPUnit\Framework\TestCase;

    class GooglePlaceDetailTest extends TestCase 
    {

        public function testGet() : void
        {            
            $apiKey = uniqid();  // some randomized mock value
            MainMenu::setOptionValue(MainMenu::SERVER_API_KEY_FIELD_NAME, $apiKey);
            $response = GooglePlaceDetail::get($placeId);
            print_r($response);
        }

        public function testGetCached() : void
        {
            // TODO: called mocked testGet()

            // TODO: SET THIS UP AS THE PHPUNIT BOOTSTRAP
            include_once dirname(__DIR__, 1) . '/bootstrap.php';
            
            // sydney opera house place id
            $placeId = 'ChIJ3S-JXmauEmsRUcIaWtf4MzE';
            $response = GooglePlaceDetail::getCached($placeId);
            print_r($response);
            
        }
    }
}