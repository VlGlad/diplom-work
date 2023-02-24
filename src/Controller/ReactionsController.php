<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Likes;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ReactionsController extends AbstractController
{
    #[Route('/app/like', name: 'app_like', methods: ['POST', 'GET'])]
    public function like(#[CurrentUser] ?User $user, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $postId = $request->request->get('postId');
        $likeEntities = $doctrine->getRepository(Likes::class)->findBy([
            'user_id' => $user->getId(),
            'target_post_id' => $postId,
        ]);
        $entityManager = $doctrine->getManager();

        if (count($likeEntities) > 0){
            foreach ($likeEntities as $like){
                $entityManager->remove($like);
            }
            $entityManager->flush();
            return $this->json(['answer' => 'ok']);   
        }

        $like = new Likes;
        $like
            ->setUserId($user->getId())
            ->setTargetPostId($postId);
        $entityManager->persist($like);
        $entityManager->flush();
        return $this->json(['answer' => 'ok']);
    }

    #[Route('/app/comment', name: 'app_comment', methods: ['POST'])]
    public function comment(#[CurrentUser] ?User $user, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $postId = $request->request->get('postId');
        $commentText = $request->request->get('comment');
        $postDate = $request->request->get('date');

        try {
            $entityManager = $doctrine->getManager();

            $comment = new Comments;
            $comment
                ->setUserId($user->getId())
                ->setTargetPostId($postId)
                ->setTextContent($commentText)
                ->setCreateDate(new \DateTime($postDate));
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->json(['answer' => 'ok']);
        } catch (Exception $ex) {
            return $this->json(['answer' => $ex->getMessage()]);
        }
    }
}
