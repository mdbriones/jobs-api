<?php
namespace App\Services;

use Goutte\Client;

class ScrapeService {
    
    public $client;
    private $results = array();

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function scrapeData($data)
    {
        foreach($data['urls'] as $url) {
            $page = $this->client->request('GET', $url);

            foreach($data['css_selectors'] as $selector) {
                $text = $page->filter($selector)->text();
                $this->results[$url] = $text;
            }

        }

        return $this->results;
    }
}