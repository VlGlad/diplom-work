<?php

namespace App\Controller;

use App\Entity\MediaFile;
use App\Entity\Post;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\MediaFileType;
use App\Form\PostType;
use App\Form\SubscriptionType;
use App\Form\UnsubscriptionType;
use App\Form\UserSearchType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ActionsController extends AbstractController
{
    #[Route('/app/search', name: 'app_searchUser')]
    public function searchUser(Request $request, ManagerRegistry $doctrine): Response
    {
        $foundUsers = [];
        // $user = new User;
        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $searchedName = $form->get('nickname')->getData();
            $foundUsers = $doctrine->getRepository(User::class)->findLikeUser($searchedName[0]);
        }
        return $this->render('page/search.html.twig', [
            "title" => "hello",
            'searchForm' => $form,
            'users' => $foundUsers,
        ]);
    }

    #[Route('/app/add', name: 'app_addpage')]
    public function addPage(#[CurrentUser] ?User $user, Request $request,
        FileUploader $fileUploader, ManagerRegistry $doctrine): Response
    {
        $post = new Post;
        $media = new MediaFile;
        $form = $this->createForm(PostType::class);

        $prefilledRequestDataBag = $this->jsonToRequestDataBag($request->getContent());

        $request->request->set($form->getName(), $prefilledRequestDataBag);
        $form->handleRequest($request);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $post->setOwnerId($user->getId());
            $mediaFile = $form->get('file_url')->getData();
            if ($mediaFile){
                $mediaFileName = $fileUploader->upload($mediaFile);
                $media
                    ->setFileUrl($mediaFileName)
                    ->setType(1)
                    ->setFileLikes(0)
                    ->setFileOwner($user->getId())
                ;
                $post->setMediaFile($media);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($media);
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->json(['Success!']);
            }
        } 
        else {
            return $this->json(['wtf']);
        }
        // return $this->render('page/add.html.twig', [
        //     "title" => "hello",
        //     'addForm' => $form,
        // ]);
    }

    #[Route('/app/user/{profile}', name: 'app_userProfile')]
    public function userPage(#[CurrentUser] ?User $user, Request $request, string $profile, ManagerRegistry $doctrine): Response
    {
        // Проверяем корректность запроса
        $profileUser = $doctrine->getRepository(User::class)->findBy(['nickname' => $profile]);
        if (count($profileUser) != 1){
            return $this->render('page/404.html.twig');
        }
        
        // Проверяем, не является ли текущая страница личной страницей текущего пользователя
        $profileUser = $profileUser[0];
        $curentUsersPage = false;
        if ($profileUser->getId() == $user->getId()){
            $curentUsersPage = true;
        }

        $profileAvatar = ''; // Сделать аватарки!!!
        $profileImages = $doctrine->getRepository(MediaFile::class)->findBy(['file_owner' => $profileUser->getId()]);

        $subbed = $doctrine->getRepository(Subscription::class)->count([
            'user_id' => $user->getId(), 'target_id' => $profileUser->getId()
        ]);

        $subscribeForm = $this->createForm(SubscriptionType::class);
        $unsubscribeForm = $this->createForm(UnsubscriptionType::class);

        // Механизм отписки
        if ($subbed){

            $unsubscribeForm->handleRequest($request);
            if ($unsubscribeForm->isSubmitted() && $unsubscribeForm->isValid()){
                $entityManager = $doctrine->getManager();
                $subscription = $entityManager->getRepository(Subscription::class)
                    ->findBy(
                        ['user_id' => $user->getId(),
                        'target_id' => $profileUser->getId()
                    ]);
                foreach ($subscription as $sub){
                    $entityManager->remove($sub);
                }
                $entityManager->flush();
                return $this->render('page/profile.html.twig', [
                    "profileUser" => $profileUser,
                    "profileAvatar" => $profileAvatar,
                    "profileImages" => $profileImages,
                    "currentUsersPage" => $curentUsersPage,
                    "form" => $subscribeForm,
                    "subbed" => false,
                ]);
            }
            return $this->render('page/profile.html.twig', [
                "profileUser" => $profileUser,
                "profileAvatar" => $profileAvatar,
                "profileImages" => $profileImages,
                "currentUsersPage" => $curentUsersPage,
                "form" => $unsubscribeForm,
                "subbed" => true,
            ]);
        }

        // Механизм подписки
        $subscription = new Subscription;
        
        $subscribeForm->handleRequest($request);
        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()){
            $subscription
                ->setUserId($user->getId())
                ->setTargetId($profileUser->getId());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($subscription);
            $entityManager->flush();
            return $this->render('page/profile.html.twig', [
                "profileUser" => $profileUser,
                "profileAvatar" => $profileAvatar,
                "profileImages" => $profileImages,
                "currentUsersPage" => $curentUsersPage,
                "form" => $unsubscribeForm,
                "subbed" => true,
            ]);
        }

        return $this->render('page/profile.html.twig', [
            "profileUser" => $profileUser,
            "profileAvatar" => $profileAvatar,
            "profileImages" => $profileImages,
            "currentUsersPage" => $curentUsersPage,
            "form" => $subscribeForm,
            "subbed" => false,
        ]);
    }
}
