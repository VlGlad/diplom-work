<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Entity\Subscription;
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
        $subscriptions = $doctrine->getRepository(Subscription::class)->findBy(['user_id' => $user->getId()]);
        $mediaFiles = [];
        foreach ($subscriptions as $sub) {
            $mediaFiles = array_merge(
                $mediaFiles, $doctrine->getRepository(MediaFile::class)->findBy(['file_owner' => $sub->getTargetId()]));
        }
        return $this->render('page/index.html.twig', [
            'images' => $mediaFiles,
        ]);
    }
}