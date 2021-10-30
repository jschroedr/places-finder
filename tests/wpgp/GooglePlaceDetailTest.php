<?php

namespace wpgp
{

    use PHPUnit\Framework\TestCase;

    class GooglePlaceDetailTest extends TestCase 
    {

        private function checkResultContent(array $response) : void
        {
            // we should get a result that contains google place detail content
            print_r($result);
            $result = $response['result'] ?? [];
            $this->assertNotEmpty($result);
        }

        public function testGet() : void
        {
            // some randomized mock values for place id and api key
            $placeId = $_ENV['WPGP_TEST_PLACE_ID'];
            $apiKey = uniqid();
            MainMenu::setOptionValue(MainMenu::SERVER_API_KEY_FIELD_NAME, $apiKey);
            $response = GooglePlaceDetail::get($placeId);
            $this->checkResultContent($response);
            $placeId = $response['result']['place_id'] ?? '';
            $this->assertNotEmpty($placeId);
            $this->assertIsString($placeId);
        }

        public function testGetCached() : void
        {
            // ensure there is cached content available
            // and the mock place id context is set
            $placeId = $_ENV['WPGP_TEST_PLACE_ID'];
            $response = GooglePlaceDetail::getCached($placeId);
            $this->checkResultContent($response);
        }
    }
}