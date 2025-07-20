<?php

namespace App\Twig\Components\Game;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class GameComponent
{
    use DefaultActionTrait;
}
