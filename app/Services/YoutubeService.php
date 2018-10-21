<?php

namespace App\Services;

use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: mnmari
 * Date: 21/10/18
 * Time: 17:54
 */
class YoutubeService
{
    private $client;

    private $key;

    private $promises = [];

    private const ENDPOINT = "https://www.googleapis.com/youtube/v3/videos";

    public function __construct()
    {
        $this->key = env('YOUTUBE_API_KEY', false);
        $this->client = new Client();
    }

    /**
     * @param array $params
     * @param string $requestId
     */
    public function listVideosAsync(array $params, string $requestId): void
    {
        if (count($params) === 0) {
            throw new InvalidArgumentException("List videos with no arguments");
        }
        $params['key'] = $this->key;
        $this->promises[$requestId] = $this->client->requestAsync('GET', self::ENDPOINT, ['query' => $params]);
    }

    /**
     * @return \GuzzleHttp\Psr7\Response[]
     */
    public function getResponses(): array
    {
        $responses = [];
        foreach ($this->promises as $requestId => $promise) {
            $response = $promise->wait();
            $responses[$requestId] = json_decode((string) $response->getBody());
        }
        $this->promises = [];
        return $responses;
    }
}
