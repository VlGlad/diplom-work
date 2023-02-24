<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Likes;
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
    public function frontpage(#[CurrentUser] ?User $user)
    {
        if (! is_null($user)){
            return $this->redirectToRoute('app_homepage');
        }
        return $this->render("page/frontpage.html.twig");
    }

    #[Route('/app', name: 'app_homepage')]
    public function browse(#[CurrentUser] ?User $user,  ManagerRegistry $doctrine): Response
    {
        $subscriptions = $doctrine->getRepository(Subscription::class)->findBy(['user_id' => $user->getId()]);
        $mediaFiles = [];
        $mediaFilesTemp = [];
        foreach ($subscriptions as $sub) {
            $mediaFilesTemp = $doctrine->getRepository(MediaFile::class)->findBy(['file_owner' => $sub->getTargetId()]);
            foreach ($mediaFilesTemp as $media){
                $media
                    ->setFileLikes(
                        $doctrine->getRepository(Likes::class)->count(['target_post_id' => $media->getId()]))
                    ->setFileComments(
                        $doctrine->getRepository(Comments::class)->findBy(['target_post_id' => $media->getId()]));
            }
            $mediaFiles = array_merge($mediaFiles, $mediaFilesTemp);
        }
        dump($mediaFiles);
        return $this->render('page/index.html.twig', [
            'images' => $mediaFiles,
        ]);
    }
}