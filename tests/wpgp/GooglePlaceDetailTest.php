<?php
/**
 * Test suite for GooglePlaceDetail.
 * 
 * PHP Version 7
 * 
 * @category TESTCASE
 * @package  WPGP
 * @author   Jake Schroeder <jake_schroeder@outlook.com>
 * @license  GNU v3
 * @link     https://github.com/jschroedr/wp-google-places
 */
namespace wpgp
{

    use PHPUnit\Framework\TestCase;

    /**
     * Test case that covers the GooglePlaceDetail class.
     * 
     * @category TESTCASE
     * @package  WPGP
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/wp-google-places
     */
    class GooglePlaceDetailTest extends TestCase 
    {

        /**
         * Helper method to check detail response content validity.
         * 
         * @param $response array: the cached or fresh place details response
         * 
         * @return void
         */
        private function _checkResultContent(array $response) : void
        {
            // we should get a result that contains google place detail content
            $this->assertNotEmpty($response);
        }

        /**
         * Test that we can get the place details using the HTTP GET API.
         * 
         * @return void
         */
        public function testGet() : void
        {
            // some randomized mock values for place id and api key
            $placeId = $_ENV['WPGP_TEST_PLACE_ID'];
            $apiKey = uniqid();
            MainMenu::setOptionValue(MainMenu::SERVER_API_KEY_FIELD_NAME, $apiKey);
            $response = GooglePlaceDetail::get($placeId);
            $this->_checkResultContent($response);
            $placeId = $response['place_id'] ?? '';
            $this->assertNotEmpty($placeId);
            $this->assertIsString($placeId);
        }

        /**
         * Test that we can get cached content using the Cache object.
         * 
         * @return void
         */
        public function testGetCached() : void
        {
            // ensure there is cached content available
            // and the mock place id context is set
            $placeId = $_ENV['WPGP_TEST_PLACE_ID'];
            $this->testGet();  // ensure there is cached content available
            $response = GooglePlaceDetail::getCached($placeId);
            $this->_checkResultContent($response);
        }
    }
}