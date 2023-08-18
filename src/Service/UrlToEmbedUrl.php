<?php

namespace App\Service;

class UrlToEmbedUrl
{
    public function toEmbedUrl(string $url): string
    {

        if (str_contains($url, 'youtube')) {
            $embedYoutubeUrl = str_replace('watch?v=', 'embed/', $url);

            return $embedYoutubeUrl;
        }

        if (str_contains($url, 'dailymotion')) {
            $embedDailymotionUrl = str_replace('/video/', '/embed/video/', $url);

            return $embedDailymotionUrl;
        }

        return 'Mauvais url, vous devez utiliser des liens en youtube.com ou dailymotion.com.';
    }
}