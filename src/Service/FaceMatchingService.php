<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

class FaceMatchingService extends ApiService
{
    protected string $endpoint = 'http://merlinface.com:12345/api/';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getMatchingResult(string $imagePath, string $name)
    {
        $result = null;
        try {
            $response = $this->client->post(
                $this->endpoint,
                [
                    'multipart' => [
                        [
                            'name'     => 'name',
                            'contents' => $name,
                        ],
                        [
                            'name'     => 'photo',
                            'contents' => Utils::tryFopen($imagePath, 'r')
                        ]
                    ]
                ]
            );
            $result = $response->getBody()->getContents();
        } catch (\Throwable $e) {
        }
        return $result;
    }

    public function getRetry(string $retryId)
    {
        $result = null;
        try {
            $response = $this->client->post(
                $this->endpoint,
                [
                    'multipart' => [
                        [
                            'name'     => 'retry_id',
                            'contents' => $retryId,
                        ],
                    ]
                ]
            );
            $result = $response->getBody()->getContents();
        } catch (\Throwable $e) {
        }
        return $result;
    }
}