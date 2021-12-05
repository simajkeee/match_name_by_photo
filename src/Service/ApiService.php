<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class ApiService
{
    protected $client;

    protected string $endpoint = '';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
}