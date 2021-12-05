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


        public function testSearchCoords() : void
        {
            // wespac bank in sydney
            $query = '-33.867275, 151.206604';
            $locations = Search::list($query);
            $this->assertTrue($locations);
        }

    }

}