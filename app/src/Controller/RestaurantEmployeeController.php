<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\RestaurantEmployee;
use App\Event\RegisterUserEvent;
use App\Form\EmployeeType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restaurant/{restaurant}/employee', requirements: ['restaurant' => '\d+'])]
class RestaurantEmployeeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    #[Route('/add', name: 'app_restaurant_employee_add')]
    public function addEmployee(Restaurant $restaurant, Request $request): Response
    {
        $employee = (new RestaurantEmployee())->setRestaurant($restaurant);
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registerUserEvent = new RegisterUserEvent($employee->getEmployee());
            $this->dispatcher->dispatch($registerUserEvent, RegisterUserEvent::REGISTER_USER);

            if ($registerUserEvent->isPropagationStopped()) {
                $errorMessage = "Can't set email {$employee->getEmployee()->getEmail()} for employee. ".
                    'User with specific e-mail already exist';
                $form->get('employee')->addError(new FormError($errorMessage));
            }
        }

        return $this->handleEmployeeForm($form);
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
        $form->handleRequest($request);

        return $this->handleEmployeeForm($form);
    }

    #[Route('/remove/{employee}', name: 'app_restaurant_employee_remove')]
    #[ParamConverter(
        'employee',
        class: RestaurantEmployee::class,
        options: ['mapping' => ['employee' => 'employee', 'restaurant' => 'restaurant']]
    )]
    public function removeEmployee(RestaurantEmployee $employee, Restaurant $restaurant): Response
    {
        $this->entityManager->remove($employee);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_restaurant_info', ['id' => $restaurant->getId()]);
    }

    private function handleEmployeeForm(FormInterface $form): Response
    {
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RestaurantEmployee $employee */
            $employee = $form->getData();
            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_restaurant_employee_edit', [
                'restaurant' => $employee->getRestaurant()->getId(),
                'employee' => $employee->getEmployee()->getId(),
            ]);
        }

        return $this->render('restaurant_employee/add.html.twig', ['form' => $form]);
    }
}
