<?php

namespace App\Twig\Components\Game;

use App\Service\StableDiffusionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class VisualPartComponent extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly StableDiffusionService $stableDiffusionService,
        private readonly HttpClientInterface    $stableDiffusionClient,
        private readonly HubInterface           $hub
    )
    {
    }

    #[LiveAction]
    public function generate(): void
    {
        $response = $this->stableDiffusionService->txt2img(
            "(masterpiece, best quality), pixel, pixel art, medieval alchemist lab, dark fantasy, first-person view, glowing potions bubbling on wooden tables, skulls and ancient scrolls scattered, bubbling cauldron emitting green light, arcane runes etched on stone floor, mysterious and magical atmosphere, <lora:pixel_f2:0.5>",
            "(worst quality, low quality:2)"
        );

        $content = json_decode($response->getContent(), true);
        $image = base64_decode($content['images'][0]);
        $src = 'data:image/png;base64,' . base64_encode($image);

        $update = new Update(
            'game',
            $this->renderView('game/visual_response.stream.html.twig', ['src' => $src])
        );
        $this->hub->publish($update);
    }
}
