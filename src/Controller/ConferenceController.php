<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ConferenceRepository;
use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class ConferenceController extends AbstractController
{
    #[Route("/", name: "homepage")]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render("conference/index.html.twig", [
            "conferences" => $conferenceRepository->findAll(),
        ]);
    }

    #[Route("/conference/{id}", name: "conference")]
    public function show(
        #[MapEntity] Conference $conference,
        CommentRepository $commentRepository,
        #[MapQueryParameter] int $offset = 0,
    ): Response {
        $offset = max(0, $offset);
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return $this->render("conference/show.html.twig", [
            "conference" => $conference,
            "comments" => $paginator,
            "previous" => $offset - CommentRepository::COMMENTS_PER_PAGE,
            "next" => min(count($paginator), $offset + CommentRepository::COMMENTS_PER_PAGE),
        ]);
    }
}
