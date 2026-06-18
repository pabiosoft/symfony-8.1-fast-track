<?php

namespace App\EventListener;

use App\Repository\ConferenceRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

final class TwigEventListener
{
    public function __construct(private Environment $twig, private ConferenceRepository $conferenceRepository) {}

    #[AsEventListener]
    public function onControllerEvent(ControllerEvent $event): void
    {
        $this->twig->addGlobal("conferences", $this->conferenceRepository->findAll());
    }
}
