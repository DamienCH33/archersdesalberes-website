<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    /**
     * Génère dynamiquement le sitemap XML à partir des pages statiques
     * et du contenu publié (articles, albums).
     */
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function index(
        ArticleRepository $articleRepository,
        AlbumRepository $albumRepository,
    ): Response {
        $urls = [];

        // Pages statiques (route => priorité => fréquence de changement)
        $staticRoutes = [
            ['route' => 'app_home', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'app_actualites', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['route' => 'app_gallery', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'app_adhesion', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'app_horaires', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['route' => 'app_history', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['route' => 'app_contact', 'priority' => '0.6', 'changefreq' => 'yearly'],
        ];

        foreach ($staticRoutes as $item) {
            $urls[] = [
                'loc' => $this->generateUrl($item['route'], [], UrlGeneratorInterface::ABSOLUTE_URL),
                'priority' => $item['priority'],
                'changefreq' => $item['changefreq'],
            ];
        }

        // Articles publiés
        foreach ($articleRepository->findPublished() as $article) {
            $urls[] = [
                'loc' => $this->generateUrl('app_article_show', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => $article->getCreatedAt()->format('Y-m-d'),
            ];
        }

        // Albums publiés
        foreach ($albumRepository->findPublished() as $album) {
            $urls[] = [
                'loc' => $this->generateUrl('gallery_album', ['slug' => $album->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'lastmod' => $album->getCreatedAt()->format('Y-m-d'),
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/index.xml.twig', ['urls' => $urls]),
            Response::HTTP_OK
        );
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }
}
