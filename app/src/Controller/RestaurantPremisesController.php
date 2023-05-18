<?php

namespace App\Controller;

use App\Entity\Premises;
use App\Entity\Restaurant;
use App\Form\PremisesType;
use App\Security\Voter\RestaurantAdminVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/premises/{restaurant}', requirements: ['restaurant' => '\d+'])]
class RestaurantPremisesController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/add', name: 'app_premises_add', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted(RestaurantAdminVoter::RESTAURANT_ADMIN, subject: 'restaurant')]
    public function add(Restaurant $restaurant, Request $request): Response
    {
        $premises = new Premises();
        $form = $this->createForm(PremisesType::class, $premises, ['attr' => ['class' => 'col-4 mx-auto']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $premises->setRestaurant($restaurant);
            $this->entityManager->persist($premises);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_restaurant_info', ['id' => $restaurant->getId()]);
        }

        return $this->render('form.html.twig', [
            'form' => $form,
        ]);
    }
}
