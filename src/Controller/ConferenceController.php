<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController]
class ConferenceController
{
    // If this class were to extend the AbstractController, you could avoid this constructor.
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route('/', name: 'homepage')]
    public function index(
        ConferenceRepository $conferenceRepository
    ): Response {
        return new Response($this->twig->render(
            'conference/index.html.twig', [
                'conferences' => $conferenceRepository->findAll(),
            ]
        ));
    }

    #[Route('/conference/{id}', name: 'conference')]
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response {
        $offset = max(0, $request->query->getInt('offset'));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render(
            'conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            ]
        ));
    }
}
