<?php

namespace App\Http\Controllers;

use App\Services\WikipediaService;
use Google_Service_YouTube;
use Illuminate\Http\JsonResponse;
use App\Services\YoutubeService;
use Illuminate\Http\Request;
use Psr\Log\InvalidArgumentException;

class YoutubeController extends Controller
{
    /**
     * @var Google_Service_YouTube
     */
    private $service;

    private $wiki;

    private const TOP_VIDEOS_COUNT = 10;

    private const COUNTRY_CODES = [
        'GB' => 'United_Kingdom',
        'NL' => 'Netherlands',
        'DE' => 'Germany',
        'FR' => 'France',
        'ES' => 'Spain',
        'IT' => 'Italy',
        'GR' => 'Greece'
    ];

    /**
     * Create a new controller instance.
     * @param YoutubeService $service
     * @param WikipediaService $wiki
     */
    public function __construct(YoutubeService $service, WikipediaService $wiki)
    {
        $this->service = $service;
        $this->wiki = $wiki;
    }

    /**
     * Get countries description and top [TOP_VIDEOS_COUNT] youtube videos. Optionally can use pagination
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function topVideos(Request $request): JsonResponse
    {
        $pageSize = $request->query('countriesPerPage');
        $page = $request->query('page');
        $countries = array_keys(self::COUNTRY_CODES);
        if ($pageSize && $page) {
            $countries = array_slice($countries, $pageSize * ($page - 1), $pageSize);
        }
        $topVideosList = $this->getYoutubeVideos($countries);
        foreach ($countries as $country_code) {
            $topVideosList[$country_code] = [
                'wiki' => $this->getWikiInfo($country_code),
                'top_videos' => $topVideosList[$country_code]
            ];
        }
        return response()->json($topVideosList);
    }

    /**
     * @param array $countryCodes
     * @return array
     */
    private function getYoutubeVideos(array $countryCodes): array
    {
        foreach ($countryCodes as $countryCode) {
            $params = [
                'part' => 'snippet',
                'chart' => 'mostPopular',
                'regionCode' => $countryCode,
                'videoCategoryId' => '',
                'maxResults' => self::TOP_VIDEOS_COUNT
            ];
            $this->service->listVideosAsync($params, $countryCode);
        }
        $responses = $this->service->getResponses();
        $videosInfo = [];
        foreach ($responses as $countryCode => $response) {
            $videosInfo[$countryCode] = [];
            foreach ($response->items as $video) {
                $videosInfo[$countryCode][] = [
                    'description' => $video->snippet->description,
                    'thumbnail' => [
                        'medium' => $video->snippet->thumbnails->medium->url,
                        'high' => $video->snippet->thumbnails->high->url
                    ]
                ];
            }
        }
        return $videosInfo;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    private function getWikiInfo(string $countryCode): string
    {
        if (!isset(self::COUNTRY_CODES[$countryCode])) {
            throw new InvalidArgumentException("Invalid country code: " . $countryCode);
        }
        return $this->wiki->getDescription(self::COUNTRY_CODES[$countryCode]);
    }
}
