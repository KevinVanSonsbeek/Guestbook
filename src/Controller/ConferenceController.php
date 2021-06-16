<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController]
final class ConferenceController extends AbstractController
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

    #[Route('/conference/{slug}', name: 'conference')]
    public function show(
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        Conference $conference,
        CommentRepository $commentRepository,
    ): Response {
        $comment = new Comment();
        $form = $formFactory->create(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);
            $photo = $form['photo']->getData();

            if ($photo) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                $photo->move($photoDir, $filename);
                $comment->setPhotoFilename($filename);
            }

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset'));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render(
            'conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
                'comment_form' => $form->createView(),
            ]
        ));
    }
}
