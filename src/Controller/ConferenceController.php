<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ConferenceRepository;
use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ConferenceController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route("/", name: "homepage")]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render("conference/index.html.twig", [
            "conferences" => $conferenceRepository->findAll(),
        ]);
    }

    #[Route("/conference/{slug:conference}", name: "conference")]
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository,
        #[Autowire("%photo_dir%")] string $photoDir,
        #[MapQueryParameter(options: ["min_range" => 0])] int $offset = 0,
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            if ($photo = $form["photo"]->getData()) {
                $filename = bin2hex(random_bytes(6)) . "." . $photo->guessExtension();
                $photo->move($photoDir, $filename);
                $comment->setPhotoFilename($filename);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute("conference", ["slug" => $conference->getSlug()]);
        }

        $offset = max(0, $offset);
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return $this->render("conference/show.html.twig", [
            "conference" => $conference,
            "comments" => $paginator,
            "previous" => $offset - CommentRepository::COMMENTS_PER_PAGE,
            "next" => min(count($paginator), $offset + CommentRepository::COMMENTS_PER_PAGE),
            "comment_form" => $form,
        ]);
    }
}
