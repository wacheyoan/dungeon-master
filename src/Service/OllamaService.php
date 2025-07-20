<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaService
{
    public function __construct(
        private readonly HttpClientInterface $ollamaClient
    )
    {
    }

    public function generate(string $prompt)
    {
        $response = $this->ollamaClient->request('POST', '/api/generate', [
            'json' => [
                'model' => 'mj-dungeon',
                'prompt' => $prompt
            ]
        ]);

        return $response;
    }

}
