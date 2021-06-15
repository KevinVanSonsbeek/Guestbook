<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/{name?World}', name: 'homepage')]
    public function index(
        string $name
    ): Response {
        return new Response($this->twig->render(
            'conference/index.html.twig', [
                'controller_name' => 'ConferenceController',
                'name' => $name,
            ]
        ));
    }
}
