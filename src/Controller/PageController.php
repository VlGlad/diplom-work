<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Likes;
use App\Entity\MediaFile;
use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;

use DateTimeImmutable;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Key\InMemory;

class PageController extends AbstractController
{
    // private 

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
        $username = $this->getUser()->getUsername();

        $key = InMemory::base64Encoded(
            'IUNoYW5nZVRoaXNNZXJjdXJlSHViSldUU2VjcmV0S2V5ISFDaGFuZ2VUaGlzTWVyY3VyZUh1YkpXVFNlY3JldEtleSEhQ2hhbmdlVGhpc01lcmN1cmVIdWJKV1RTZWNyZXRLZXkhIUNoYW5nZVRoaXNNZXJjdXJlSHViSldUU2VjcmV0S2V5ISFDaGFuZ2VUaGlzTWVyY3VyZUh1YkpXVFNlY3JldEtleSEhQ2hhbmdlVGhpc01lcmN1cmVIdWJKV1RTZWNyZXRLZXkhIUNoYW5nZVRoaXNNZXJjdXJlSHViSldUU2VjcmV0S2V5ISFDaGFuZ2VUaGlzTWVyY3VyZUh1YkpXVFNlY3JldEtleSEhQ2hhbmdlVGhpc01lcmN1cmVIdWJKV1RTZWNyZXRLZXkhIUNoYW5nZVRoaXNNZXJjdXJlSHViSldUU2VjcmV0S2V5IQ=='
        );
        
        $token = (new JwtFacade())->issue(
            new Sha256(),
            $key,
            static fn (
                Builder $builder,
                DateTimeImmutable $issuedAt
            ): Builder => $builder
                ->withClaim('mercure', ['subscribe' => [sprintf("/%s", $username)]])
                ->expiresAt($issuedAt->modify('+60 minutes'))
        );

        $response =  $this->render('page/index2.html.twig', [
            'user' => $username,
        ]);

        $response->headers->clearCookie("mercureAuthorization");

        $response->headers->setCookie(
            Cookie::create(
                'mercureAuthorization',
                $token->toString(),
                (new \DateTime())->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',
                'localhost',
                false,
                true,
                false,
                null
            )
        );
        
        return $response;
    }

    #[Route("/app/chat", name: "app_chat")]
    public function chat()
    {
        // $username = $this->getUser()->getUsername();

        // return $this->render('page/index2.html.twig', [
        //     'user' => $username,
        // ]);
    }

    #[Route("/app/getfeed", name: "app_getfeed")]
    public function getFeed(#[CurrentUser] ?User $user,  ManagerRegistry $doctrine)
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
        return $this->json(['posts' => $mediaFiles]);
    }
}