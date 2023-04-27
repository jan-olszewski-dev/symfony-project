<?php

namespace App\EventSubscriber;

use App\Entity\Premises;
use App\Entity\RestaurantEmployee;
use App\Entity\RestaurantRole;
use App\Entity\User;
use App\Event\CreateRestaurantEvent;
use App\Repository\PremisesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/** @SuppressWarnings(PHPMD.MissingImport) */
class CreateRestaurantSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function addCreatorAsAdminToRestaurant(CreateRestaurantEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('Unable to create restaurant as anonymous user');
        }

        /** @var RestaurantRole $adminRole */
        $adminRole = $this->entityManager
            ->getRepository(RestaurantRole::class)
            ->findOneBy(['role' => RestaurantRole::ADMIN]);

        $restaurantEmployee = (new RestaurantEmployee())
            ->setEmployee($user)
            ->addRole($adminRole);

        $event->getRestaurant()->addEmployee($restaurantEmployee);
    }

    public function createRestaurant(CreateRestaurantEvent $event): void
    {
        /** @var PremisesRepository $repository */
        $repository = $this->entityManager->getRepository(Premises::class);
        $repository->save($event->getPremises())->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreateRestaurantEvent::NAME => [
                ['addCreatorAsAdminToRestaurant', 10],
                ['createRestaurant', -10],
            ],
        ];
    }
}
