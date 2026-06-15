<?php

namespace App\Controller\Front;

use App\Repository\AlbumRepository;
use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'app_gallery')]
    public function index(AlbumRepository $albumRepository): Response
    {
        return $this->render('gallery/index.html.twig', [
            'albums' => $albumRepository->findPublished(),
        ]);
    }

    #[Route('/gallery/{slug}', name: 'gallery_album')]
    public function album(string $slug, AlbumRepository $albumRepository): Response
    {
        $album = $albumRepository->findOnePublishedBySlug($slug);

        if (!$album) {
            throw $this->createNotFoundException('Cet album n\'existe pas ou n\'est pas publié.');
        }

        return $this->render('gallery/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/photo/{id}', name: 'photo_show')]
    public function photo(string $id, PhotoRepository $photoRepository): Response
    {
        $photo = $photoRepository->find($id);

        if (!$photo || !$photo->getAlbum()?->isPublished()) {
            throw $this->createNotFoundException();
        }

        return $this->render('photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }
}
