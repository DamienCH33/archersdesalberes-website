<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;

#[Route('/admin/photo-import', name: 'admin_photo_batch')]
#[IsGranted('ROLE_ADMIN')]
class PhotoBatchUploadController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createFormBuilder()
            ->add('album', EntityType::class, [
                'class' => Album::class,
                'choice_label' => 'title',
                'label' => 'Album',
                'placeholder' => '— Choisir un album —',
            ])
            ->add('images', FileType::class, [
                'label' => 'Photos (sélection multiple)',
                'multiple' => true,
                'mapped' => false,
                'constraints' => [new All([new Image(maxSize: '8M')])],
            ])
            ->add('upload', SubmitType::class, ['label' => 'Importer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $album = $form->get('album')->getData();
            /** @var UploadedFile[] $files */
            $files = $form->get('images')->getData();

            $dir = $this->getParameter('kernel.project_dir').'/public/uploads/photos';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $count = 0;
            foreach ($files as $file) {
                $safe = $slugger->slug(pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME));
                $name = $safe.'-'.uniqid().'.'.$file->guessExtension();
                $file->move($dir, $name);

                $photo = (new Photo())
                    ->setPath($name)
                    ->setAlbum($album);
                $em->persist($photo);
                ++$count;
            }
            $em->flush();

            $this->addFlash('success', $count.' photo(s) importée(s).');

            return $this->redirectToRoute('admin_photo_batch');
        }

        return $this->render('admin/photo_batch.html.twig', ['form' => $form]);
    }
}
