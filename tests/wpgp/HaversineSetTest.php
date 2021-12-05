<?php


namespace wpgp
{

    use PHPUnit\Framework\TestCase;

    class HaversineSetTest extends TestCase
    {

        public function testOrder() : void
        {

            // setup the test posts
            $posts = getAndSetTestPosts();

            // turn them into locations
            $locations = [];
            
            // when performing a haversine order we will simply use the 
            // cached coords and WILL NOT request full place details 
            // for the sake of performance
            $cacheOnly = true;
            foreach ($posts as $post) {
                $locations[] = new Location($post->ID, $cacheOnly);
                $id = $post->ID;
                $title = $post->post_title;
                print("$id: $title\n");
            }

            // who is nearest to circular quay train station?
            $lat = -33.858455;
            $lng = 151.2021369;
            $point = new Point($lat, $lng);

            // order them around the point and check ordering
            $set = new HaversineSet($point);
            $set->order($locations);

            $order = [];
            foreach ($locations as $location) {
                $id = $location->postId;
                $title = $location->name;
                $distance = is_null($location->distance) ? 'NO DISTANCE' : $location->distance->getKilometers();
                print("$id: $title, $distance km from Circular Quay\n");
                $order[] = $id;
            }

            $expectedOrder = [9000, 9002, 9001, 9003];
            $this->assertEquals(
                $expectedOrder,
                $order,
                'The haversine distance calculation did not return the proper order'
            );
        }

    }

}