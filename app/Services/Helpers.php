<?php

namespace App\Services;

use GregKos\GreekStrings\GreekString;
use Str;

class Helpers
{
    public static function generateMapAddress($address = '', $area = '', $region = ''): string
    {
        $transliterationHelper = (new GreekString);
        $address = $transliterationHelper->setString($address ?? '')->transliterate();
        $area = $transliterationHelper->setString($area ?? '')->transliterate();
        $region = $transliterationHelper->setString($region ?? '')->transliterate();
        if ($area != '') {
            $address = $address . ', ' . $area;
        }
        if ($region != '') {
            $address = $address . ', ' . $region;
        }
        return Str::of($address)->replace('&', '%26')->replace(' ', '+');
    }
}
