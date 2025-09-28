<?php

namespace App\Service\Prestashop;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    public function __construct(
        private HttpClientInterface $client,
        private string $baseUrl,
        private string $apiKey
    ) {}

    public function getProducts(): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/products', [
            'query' => [
                'output_format' => 'JSON',
                'display' => 'full'
            ],
            'auth_basic' => [$this->apiKey, '']
        ]);
        return $response->toArray();
    }

    public function getCategories(): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/categories', [
            'query' => [
                'output_format' => 'JSON',
                'display'       => 'full',
                'sort'          => 'level_depth_ASC',
            ],
            'auth_basic' => [$this->apiKey, '']
        ]);
        return $response->toArray();
    }
}
