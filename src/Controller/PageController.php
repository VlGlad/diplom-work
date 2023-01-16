<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Entity\User;
use App\Form\MediaFileType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_frontpage')]
    public function frontpage()
    {
        return $this->render("page/frontpage.html.twig");
    }

    #[Route('/app', name: 'app_homepage')]
    public function browse(#[CurrentUser] ?User $user,  ManagerRegistry $doctrine): Response
    {
        $mediaFiles = $doctrine->getRepository(MediaFile::class)->findBy(['file_owner' => $user->getId()]);
        dump($user);
        return $this->render('page/index.html.twig', [
            'images' => $mediaFiles,
        ]);
    }

    /* #[Route('/app/search', name: 'app_searchUser')]
    public function searchUser()
    {
        
    } */

    #[Route('/app/add', name: 'app_addpage')]
    public function index(#[CurrentUser] ?User $user, Request $request, FileUploader $fileUploader, ManagerRegistry $doctrine, LoggerInterface $logger): Response
    {
        $media = new MediaFile();
        $form = $this->createForm(MediaFileType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $media = $form->getData();
            $mediaFile = $form->get('file_url')->getData();
            if ($mediaFile){
                $mediaFileName = $fileUploader->upload($mediaFile);
                $media
                    ->setFileUrl($mediaFileName)
                    ->setType(1)
                    ->setFileLikes(0)
                    ->setFileOwner($user->getId())
                ;
                $entityManager = $doctrine->getManager();
                $entityManager->persist($media);
                $entityManager->flush();
                return $this->redirectToRoute('app_homepage');
            }
        }
        return $this->render('page/add.html.twig', [
            "title" => "hello",
            'addForm' => $form,
        ]);
    }
}