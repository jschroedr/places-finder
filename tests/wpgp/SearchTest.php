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

    class SearchTest extends TestCase 
    {
        public function testListCoords() : void
        {
            getAndSetTestPosts();

            // wespac bank in sydney
            $query = '-33.867275, 151.206604';
            $result = Search::list($query);
            $this->assertTrue($result->success);
            print("\n\n");
            print("Distances from Wespac Headquarters in Sydney:\n");
            foreach ($result->locations as $location) {
                if (is_null($location->distance) === false) {
                    $distance = $location->distance->getKilometers();
                } else {
                    $distance = 'NO DISTANCE';
                }
                print("{$location->name} - {$distance} km\n");
            }
        }


        public function testListAddress() : void
        {
            getAndSetTestPosts();

            $query = '3 Cumberland St, Sydney, New South Wales 2000';
            $result = Search::list($query);
            $this->assertTrue($result->success);
            print("\n\n");
            print("Distances from Sydney Harbor Bridge:\n");
            foreach ($result->locations as $location) {
                if (is_null($location->distance) === false) {
                    $distance = $location->distance->getKilometers();
                } else {
                    $distance = 'NO DISTANCE';
                }
                print("{$location->name} - {$distance} km\n");
            }
        }

    }

}