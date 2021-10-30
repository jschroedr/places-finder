<?php


namespace wpgp
{
    class MockHelper
    {

        public static function getResponseContent(string $filename) : string
        {
            $filepath = __DIR__ . '/data/' . $filename;
            return file_get_contents($filepath);
        }

    }
}