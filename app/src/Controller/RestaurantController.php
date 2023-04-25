<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\CreateRestaurantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('', name: 'app_restaurant_list', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $this->entityManager->getRepository(Restaurant::class)->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_info', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    public function info(?Restaurant $restaurant): Response
    {
        if (!$restaurant) {
            $this->redirectToRoute('app_restaurant_list');
        }

        return $this->render('restaurant/info.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/create', name: 'app_restaurant_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(CreateRestaurantType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return $this->redirectToRoute('app_restaurant_list');
        }

        return $this->render('defaultForm.html.twig', [
            'form' => $form,
        ]);
    }
}
