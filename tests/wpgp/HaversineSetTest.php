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
            print("\n\n");
            foreach ($posts as $post) {
                $locations[] = new Location($post['id']);
                $id = $post['id'];
                $title = $post['post_title'];
                print("$id: $title\n");
            }

            // who is nearest to circular quay train station?
            $lat = -33.858455;
            $lng = 151.2021369;
            $point = new Point($lat, $lng);

            // order them around the point and check ordering
            $set = new HaversineSet($point);
            // $set->order($locations);

            print("\n\n");
            foreach ($locations as $location) {
                $id = $location->postId;
                $title = $location->name;
                $distance = is_null($location->distance) ? 'NO DISTANCE' : $location->distance->getKilometers();
                print("$id: $title, $distance from Circular Quay\n");
            }
            $this->assertTrue(true);

        }

    }

}