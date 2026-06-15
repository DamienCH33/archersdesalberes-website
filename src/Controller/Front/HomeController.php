<?php

namespace App\Controller\Front;

use App\Repository\ArticleRepository;
use App\Repository\ClubStatRepository;
use App\Repository\PartnerRepository;
use App\Repository\SettingRepository;
use App\Repository\TeamMemberRepository;
use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ArticleRepository $articleRepo,
        ClubStatRepository $statRepo,
        TeamMemberRepository $teamRepo,
        PartnerRepository $partnerRepo,
        SettingRepository $settingRepo,
        AlbumRepository $albumRepository,
        PhotoRepository $photoRepository
    ): Response {
        $statsArray = $statRepo->getAllAsArray();

        $equipe = $teamRepo->findAllForDisplay();

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
            'stats' => [
                'membres' => $statsArray['membres_actifs'] ?? 127,
                'annees' => $statsArray['annees_existence'] ?? 39,
                'competitions' => $statsArray['competitions_an'] ?? 24,
                'podiums' => $statsArray['podiums_annee'] ?? 15,
            ],
            'equipe' => $equipe,
            'actualites' => $actualites,
            'galerie' => $galerie,
            'settings' => $settings,
            'partners' => $partners,
            'sliderPhotos' => $sliderPhotos,
        ]);
    }
}
