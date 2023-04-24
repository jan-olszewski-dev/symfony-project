<?php

namespace App\Controller;

use App\Event\RegisterUserEvent;
use App\Form\RegisterUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register')]
class RegisterUserController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    #[Route('', name: 'app_register_user', methods: [Request::METHOD_GET, Request::METHOD_POST])]
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

    #[Route(
        '/{social}',
        name: 'app_social_check',
        requirements: ['social' => 'google|linkedin|facebook'],
        methods: [Request::METHOD_GET]
    )]
    public function socialCheck(): void
    {
    }
}
