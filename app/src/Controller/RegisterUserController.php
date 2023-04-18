<?php

namespace App\Controller;

use App\Event\RegisterUser\RegisterGoogleUserEvent;
use App\Event\RegisterUser\RegisterSocialUserEvent;
use App\Event\RegisterUser\RegisterUserEvent;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use League\OAuth2\Client\Provider\GoogleUser;
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
}
