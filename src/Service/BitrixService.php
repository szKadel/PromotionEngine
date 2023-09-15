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
        $this->bitrixWebhookUrl = "https://properties.bitrix24.pl/rest/9/9ez62r0fjz5if0jf";
    }

    public function call(string $method,array $data):array
    {
        $response = $this->httpClient->request(
            'POST', $this->bitrixWebhookUrl."/".$method, $data);

        return $response->toArray();
    }
}