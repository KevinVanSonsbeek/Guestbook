<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController
{
    public function __construct(
        private Environment $twig,
        private ConferenceRepository $conferenceRepository
    ) {
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $conferences = $this->conferenceRepository->findAll();

        return new Response($this->twig->render(
            'conference/index.html.twig', [
                'conferences' => $conferences,
            ]
        ));
    }

    #[Route('/conference/{id}', name: 'conference')]
    public function show(
        Conference $conference,
        CommentRepository $commentRepository
    ): Response {
        $comments = $commentRepository->findBy(['conference' => $conference]);

        return new Response($this->twig->render(
            'conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $comments,
            ]
        ));
    }
}
