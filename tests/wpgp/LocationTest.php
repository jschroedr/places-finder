<?php
/**
 * Test suite for Location.
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
     * Test case that covers the Location class.
     * 
     * @category TESTCASE
     * @package  WPGP
     * @author   Jake Schroeder <jake_schroeder@outlook.com>
     * @license  GNU v3
     * @link     https://github.com/jschroedr/wp-google-places
     */
    class LocationTest extends TestCase 
    {

        /**
         * Ensure a valid place id (and place details response)
         * results in valid (full) initialization by post id.
         * 
         * @return void
         */
        public function testInitializeValid() : void
        {
            $post = getTestPlaceIdPost();
            $location = new Location($post->ID);
            $this->assertTrue($location->initialized);
            $this->assertNotEquals($location->lat, $location::INVALID_COORD);
            $this->assertNotEquals($location->lng, $location::INVALID_COORD);
        }

        public function testInitializeValidCache() : void
        {
            $post = getTestPlaceIdPost();
            // only initialize partially using the cache
            // for performance reasons
            $location = new Location($post->ID, true);
            $this->assertTrue($location->initialized);
            $this->assertNotEquals($location->lat, $location::INVALID_COORD);
            $this->assertNotEquals($location->lng, $location::INVALID_COORD);
        }

        /**
         * Ensure a invalid place id (empty, for example)
         * still results in valid initialization by post id.
         * 
         * @return void
         */
        public function testInitializeInvalid() : void
        {
            $post = getTestPlaceIdPostEmpty();
            $location = new Location($post->ID);
            $this->assertFalse($location->initialized);
            $this->assertEquals($location->lat, $location::INVALID_COORD);
            $this->assertEquals($location->lng, $location::INVALID_COORD);
        }
    }
}