<?php

namespace App\Service;

class UrlToEmbedUrl
{
    public function toEmbedUrl($url): string
    {

        if (str_contains($url, 'youtube')) {
            $embedUrl = str_replace('watch?v=', 'embed/', $url);

            return $embedUrl;
        }

        if (str_contains($url, 'dailymotion')) {
            $embedUrl = str_replace('video/', 'embed/video/', $url);

            return $embedUrl;
        }

        return 'Mauvais url, vous devez utiliser des liens youtube ou dailymotion.';
    }
}