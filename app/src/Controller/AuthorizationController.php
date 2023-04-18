<?php

namespace App\Controller;

use App\Form\LoginType;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/auth')]
class AuthorizationController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('security/login.html.twig', [
            'form' => $this->createForm(LoginType::class),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: [Request::METHOD_GET])]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/google', name: 'app_google_login', methods: [Request::METHOD_GET])]
    public function googleLogin(GoogleClient $client): Response
    {
        return $client->redirect();
    }
}
