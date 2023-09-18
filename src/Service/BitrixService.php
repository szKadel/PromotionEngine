<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BitrixService
{
    private $httpClient;
    private $bitrixWebhookUrl;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->bitrixWebhookUrl = "";
    }

    public function call(string $method,array $data):array
    {
        $response = $this->httpClient->request(
            'POST', $this->bitrixWebhookUrl."/".$method, $data);

        return $response->toArray();
    }
}
