<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
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
}