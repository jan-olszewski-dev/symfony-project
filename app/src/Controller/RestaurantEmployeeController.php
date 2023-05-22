<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\RestaurantEmployee;
use App\Form\EmployeeType;
use App\Repository\RestaurantEmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restaurant/{restaurant}/employee', requirements: ['restaurant' => '\d+'])]
class RestaurantEmployeeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/add', name: 'app_restaurant_employee_add')]
    public function addEmployee(Restaurant $restaurant, Request $request): Response
    {
        $employee = (new RestaurantEmployee())->setRestaurant($restaurant);
        $form = $this->createForm(EmployeeType::class, $employee);

        return $this->handleEmployeeForm($form, $request);
    }

    #[Route('/edit/{employee}', name: 'app_restaurant_employee_edit')]
    #[ParamConverter(
        'employee',
        class: RestaurantEmployee::class,
        options: ['mapping' => ['employee' => 'employee', 'restaurant' => 'restaurant']]
    )]
    public function editEmployee(RestaurantEmployee $employee, Request $request): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->get('employee')->remove('plainPassword');

        return $this->handleEmployeeForm($form, $request);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return Response
     */
    private function handleEmployeeForm(FormInterface $form, Request $request): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RestaurantEmployee $employee */
            $employee = $form->getData();
            /** @var RestaurantEmployeeRepository $repository */
            $repository = $this->entityManager->getRepository(RestaurantEmployee::class);
            $repository->save($employee)->flush();

            return $this->redirectToRoute('app_restaurant_employee_edit', [
                'restaurant' => $employee->getRestaurant()->getId(),
                'employee' => $employee->getEmployee()->getId(),
            ]);
        }

        return $this->render('restaurant_employee/add.html.twig', ['form' => $form]);
    }
}
