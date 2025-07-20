<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class StableDiffusionService
{
    public function __construct(
        private readonly HttpClientInterface $stableDiffusionClient
    )
    {
    }

    public function txt2img(
        string $prompt,
        string $negativePrompt = "",
        int    $seed = -1,
        int    $steps = 30,
        int    $width = 1920,
        int    $height = 1080,
        array  $overrideSettings = [
            "sd_model_checkpoint" => "lyriel_v14.safetensors",
            "sd_lora" => "pixel_f2.safetensors"
        ]
    )
    {
        return $this->stableDiffusionClient->request('POST', '/sdapi/v1/txt2img',
            [
                'json' => [
                    "prompt" => $prompt,
                    "negative_prompt" => $negativePrompt,
                    "seed" => $seed,
                    "steps" => $steps,
                    "width" => $width,
                    "height" => $height,
                    "override_settings" => $overrideSettings,
                ],
            ]
        );
    }

}
