<?php

namespace App\Controller;

use App\Event\RegisterUserEvent;
use App\Form\RegisterUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterUserController extends AbstractController
{

    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    #[Route('/register', name: 'app_register_user')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_page');
        }

        $form = $this->createForm(RegisterUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new RegisterUserEvent($form->getData()), RegisterUserEvent::NAME);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
