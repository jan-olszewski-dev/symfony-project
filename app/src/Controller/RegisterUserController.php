<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterUserController extends AbstractController
{

    public function __construct(
        private UserRepository              $repository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    #[Route('/register', name: 'app_register_user')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->repository->save($user)->flush();
        }

        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
