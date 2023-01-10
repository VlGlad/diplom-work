<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Form\MediaFileType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function browse(ManagerRegistry $doctrine): Response
    {
        $mediaFiles = $doctrine->getRepository(MediaFile::class)->findAll();
        dump($mediaFiles);
        return $this->render('page/index.html.twig', [
            'images' => $mediaFiles,
        ]);
    }

    #[Route('/add', name: 'app_addpage')]
    public function index(Request $request, FileUploader $fileUploader, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $media = new MediaFile();
        $form = $this->createForm(MediaFileType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $logger->info('FORM IS SUBMITTED');
            $media = $form->getData();
            $mediaFile = $form->get('file_url')->getData();
            if ($mediaFile){
                $logger->info('FILE NAME IS FINE');
                $mediaFileName = $fileUploader->upload($mediaFile);
                $media->setFileUrl($mediaFileName);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($media);
                $entityManager->flush();
                return $this->redirectToRoute('app_homepage');
            }
        }
        return $this->render('page/add.html.twig', [
            "title" => "hello",
            'form' => $form,
        ]);
    }
}