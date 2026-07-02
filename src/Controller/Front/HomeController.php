<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Repository\AlbumRepository;
use App\Repository\ArticleRepository;
use App\Repository\PartnerRepository;
use App\Repository\PhotoRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ArticleRepository $articleRepo,
        PartnerRepository $partnerRepo,
        SettingRepository $settingRepo,
        AlbumRepository $albumRepository,
        PhotoRepository $photoRepository,
    ): Response {
        $actualites = $articleRepo->findLatestPublished(6);
        $galerie = $albumRepository->findLatestPublished(6);
        $settings = $settingRepo->getAllAsArray();
        $partners = $partnerRepo->findAllForDisplay();

        $sliderPhotos = $photoRepository->createQueryBuilder('p')
            ->join('p.album', 'a')
            ->where('a.isPublished = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('index.html.twig', [
            'actualites' => $actualites,
            'galerie' => $galerie,
            'settings' => $settings,
            'partners' => $partners,
            'sliderPhotos' => $sliderPhotos,
        ]);
    }
}
