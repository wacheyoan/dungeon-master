<?php

namespace App\Twig\Components\Game;

use App\Service\OllamaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class TextualPartComponent extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly OllamaService       $ollamaService,
        private readonly HttpClientInterface $ollamaClient,
        private readonly HubInterface        $hub
    )
    {
    }

    #[LiveProp(writable: true, onUpdated: 'updateResponse')]
    public ?string $prompt = null;

    public ?string $response = null;

    public function updateResponse(): void
    {
        $response = $this->ollamaService->generate($this->prompt);
        foreach ($this->ollamaClient->stream($response) as $chunk) {
            $json = $chunk->getContent();
            if (empty($json)) {
                continue;
            }
            $data = json_decode($json);
            if(!$data) {
                continue;
            }
            $this->response .= $data->response;
            if($this->response === '' && $data->done) {
                break;
            }
            $update = new Update(
                'game',
                $this->renderView('game/response.stream.html.twig', ['response' => $this->response])
            );
            $this->hub->publish($update);
            if ($data->done) {
                break;
            }
        }
    }
}
