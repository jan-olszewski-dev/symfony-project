<?php

namespace App\DataFixtures;

use App\Entity\RestaurantEmployee;
use App\Entity\RestaurantRole;
use App\Entity\User;
use App\Entity\UserRole;
use App\Repository\RestaurantRepository;
use App\Repository\RestaurantRoleRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RestaurantEmployeeFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        private readonly UserRepository           $userRepository,
        private readonly RestaurantRepository     $restaurantRepository,
        private readonly RestaurantRoleRepository $restaurantRoleRepository
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->restaurantRepository->findAll() as $restaurant) {
            foreach ($this->getEmployees() as $employee) {
                $employee->setRestaurant($restaurant);
                $manager->persist($employee);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RestaurantFixtures::class, UserFixtures::class];
    }

    public static function getGroups(): array
    {
        return [RestaurantFixtures::RESTAURANT_GROUP, UserFixtures::USER_GROUP];
    }

    private function getEmployees(): \Generator
    {
        yield from $this->getAdminEmployee();

        /** @var RestaurantRole $employeeRole */
        $employeeRole = $this->restaurantRoleRepository->findOneBy(['role' => RestaurantRole::EMPLOYEE]);
        /** @var User $user */
        $users = $this->userRepository->findAll();
        foreach (array_rand($users, 5) as $key) {
            $user = $users[$key];
            if (!in_array(UserRole::ADMIN, $user->getRoles())) {
                yield (new RestaurantEmployee())
                    ->setEmployee($user)
                    ->addRole($employeeRole);
            }
        }
    }

    public function getAdminEmployee(): \Generator
    {
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER);
        /** @var RestaurantRole $adminRole */
        $adminRole = $this->restaurantRoleRepository->findOneBy(['role' => RestaurantRole::ADMIN]);

        yield (new RestaurantEmployee())
            ->setEmployee($adminUser)
            ->addRole($adminRole);
    }
}
