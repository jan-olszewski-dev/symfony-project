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
    private RestaurantRole $adminRole;
    private RestaurantRole $employeeRole;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RestaurantRepository $restaurantRepository,
        RestaurantRoleRepository $restaurantRoleRepository
    ) {
        /** @var RestaurantRole $adminRole */
        $adminRole = $restaurantRoleRepository->findOneBy(['role' => RestaurantRole::ADMIN]);
        $this->adminRole = $adminRole;
        /** @var RestaurantRole $employeeRole */
        $employeeRole = $restaurantRoleRepository->findOneBy(['role' => RestaurantRole::EMPLOYEE]);
        $this->employeeRole = $employeeRole;
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

        /** @var User $user */
        $users = $this->userRepository->findAll();
        foreach (array_rand($users, 5) as $key) {
            $user = $users[$key];
            if (!in_array(UserRole::ADMIN, $user->getRoles())) {
                yield (new RestaurantEmployee())
                    ->setEmployee($user)
                    ->addRole($this->employeeRole);
            }
        }
    }

    public function getAdminEmployee(): \Generator
    {
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER);

        yield (new RestaurantEmployee())
            ->setEmployee($adminUser)
            ->addRole($this->adminRole);
    }
}
