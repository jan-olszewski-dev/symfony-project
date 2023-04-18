<?php

namespace App\Controller;

use App\Event\RegisterUser\RegisterFacebookUserEvent;
use App\Event\RegisterUser\RegisterGoogleUserEvent;
use App\Event\RegisterUser\RegisterLinkedInUserEvent;
use App\Event\RegisterUser\RegisterSocialUserEvent;
use App\Event\RegisterUser\RegisterUserEvent;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Client\Provider\LinkedInClient;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register')]
class RegisterUserController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private UserRepository           $repository
    )
    {
    }

    #[Route('', name: 'app_register_user')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_page');
        }

        $form = $this->createForm(RegisterUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new RegisterUserEvent($form->getData()), RegisterUserEvent::NAME);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/google', name: 'app_google_check', methods: ['GET'])]
    public function googleCheck(GoogleClient $client, Security $security): Response
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $client->fetchUser();
        $this->dispatcher->dispatch(new RegisterGoogleUserEvent($googleUser), RegisterSocialUserEvent::NAME);
        $user = $this->repository->findOneBy(['googleSubId' => $googleUser->getId()]);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user to sign in');
        }

        $security->login($user);
        return $this->redirectToRoute('app_home_page');
    }

    #[Route('/linkedin', name: 'app_linkedin_check', methods: ['GET'])]
    public function linkedInCheck(LinkedInClient $client, Security $security): Response
    {
        /** @var LinkedInResourceOwner $linkedInUser */
        $linkedInUser = $client->fetchUser();
        $this->dispatcher->dispatch(new RegisterLinkedInUserEvent($linkedInUser), RegisterSocialUserEvent::NAME);
        $user = $this->repository->findOneBy(['linkedInSubId' => $linkedInUser->getId()]);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user to sign in');
        }

        $security->login($user);
        return $this->redirectToRoute('app_home_page');
    }

    #[Route('/facebook', name: 'app_facebook_check', methods: ['GET'])]
    public function facebookCheck(FacebookClient $client, Security $security): Response
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $client->fetchUser();
        $this->dispatcher->dispatch(new RegisterFacebookUserEvent($facebookUser), RegisterSocialUserEvent::NAME);
        $user = $this->repository->findOneBy(['facebookSubId' => $facebookUser->getId()]);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user to sign in');
        }

        $security->login($user);
        return $this->redirectToRoute('app_home_page');
    }
}
