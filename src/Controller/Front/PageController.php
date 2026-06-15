<?php
// src/Controller/Front/PageController.php

namespace App\Controller\Front;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/adhesion', name: 'app_adhesion')]
    public function adhesion(): Response
    {
        return $this->render('front/adhesion.html.twig');
    }

    #[Route('/horaires', name: 'app_horaires')]
    public function horaires(): Response
    {
        return $this->render('front/horaires.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('front/contact.html.twig');
    }

    #[Route('/actualites', name: 'app_actualites')]
    public function actualites(Request $request, ArticleRepository $articleRepository): Response
    {
        $category = $request->query->get('category', 'all');

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        if ($category === 'all') {
            $articles = $articleRepository->findBy(
                ['isPublished' => true],
                ['publishedAt' => 'DESC'],
                $limit,
                $offset
            );
            $totalArticles = $articleRepository->count(['isPublished' => true]);
        } else {
            $articles = $articleRepository->findBy(
                ['isPublished' => true, 'category' => $category],
                ['publishedAt' => 'DESC'],
                $limit,
                $offset
            );
            $totalArticles = $articleRepository->count(['isPublished' => true, 'category' => $category]);
        }

        $totalPages = ceil($totalArticles / $limit);

        return $this->render('front/actualites.html.twig', [
            'articles' => $articles,
            'currentCategory' => $category,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalArticles' => $totalArticles,
        ]);
    }

    #[Route('/history', name: 'app_history')]
    public function history(): Response
    {
        return $this->render('front/history.html.twig');
    }

    #[Route('/club', name: 'app_club')]
    public function club(): Response
    {
        return $this->render('front/club.html.twig');
    }

    #[Route('/article/{slug}', name: 'app_article_show')]
    public function articleShow(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug, 'isPublished' => true]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
}
