<?php

namespace wpgp {

    class BlockRating extends BlockBase {


        public static function ratingToStars(
            float $rating,
            string $htmlStyle,
            int $totalStars = 5
        ) : string {
            $stars = [];

            // get the full stars
            $fullStars = floor($rating);
            for ($i = 0; $i < $totalStars; $i ++) {
                $symbol = $fullStars > 0 ? '&starf;' : '&star;';
                $stars[] = "<span style='$htmlStyle'>$symbol</span>";
            }
            return implode('', $stars);
        }
    
    }

}
