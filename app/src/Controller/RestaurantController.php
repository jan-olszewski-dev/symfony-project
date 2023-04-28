<?php

namespace App\Controller;

use App\Attribute\NotFoundRedirect;
use App\Entity\Premises;
use App\Entity\Restaurant;
use App\Entity\UserRole;
use App\Event\CreateRestaurantEvent;
use App\Form\CreateRestaurantType;
use App\Security\Voter\RestaurantAdminVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    #[Route('', name: 'app_restaurant_list', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $this->entityManager->getRepository(Restaurant::class)->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_restaurant_info', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
    #[IsGranted(RestaurantAdminVoter::RESTAURANT_ADMIN, subject: 'restaurant')]
    #[NotFoundRedirect(path: 'app_restaurant_list', scope: 'restaurant')]
    public function info(?Restaurant $restaurant): Response
    {
        return $this->render('restaurant/info.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/create', name: 'app_restaurant_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted(UserRole::USER)]
    public function create(Request $request): Response
    {
        $premises = new Premises();
        $form = $this->createForm(CreateRestaurantType::class, $premises);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new CreateRestaurantEvent($premises), CreateRestaurantEvent::NAME);

            return $this->redirectToRoute('app_restaurant_list');
        }

        return $this->render('defaultForm.html.twig', [
            'form' => $form,
        ]);
    }
}
