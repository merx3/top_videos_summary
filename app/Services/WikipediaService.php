<?php
/**
 * Created by PhpStorm.
 * User: mnmari
 * Date: 21/10/18
 * Time: 19:09
 */

namespace App\Services;

use GuzzleHttp\Client;

class WikipediaService
{
    private $client;

    private const ENDPOINT = "https://en.wikipedia.org/w/api.php";

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getDescription($page)
    {
        $params = [
            'format' => 'json',
            'action' => 'query',
            'prop' => 'extracts',
            'exintro' => true,
            'explaintext' => true,
            'redirects' => 1,
            'titles' => $page
        ];
        $response = $this->client->request('GET', self::ENDPOINT, ['query' => $params]);
        $pages = json_decode($response->getBody())->query->pages;
        return reset($pages)->extract;
    }
}
