<?php

namespace App\Tests\Entity;

use App\Entity\RestaurantEmployee;
use App\Entity\RestaurantRole;
use App\Repository\RestaurantRoleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestaurantEmployeeTest extends KernelTestCase
{
    public static function createValidRestaurantEmployee(): RestaurantEmployee
    {
        /** @var RestaurantRoleRepository $userRoleRepository */
        $userRoleRepository = static::getContainer()->get(RestaurantRoleRepository::class);
        $employeeRole = $userRoleRepository->findOneBy(['role' => RestaurantRole::ADMIN]);

        return (new RestaurantEmployee())
            ->setEmployee(UserTest::createValidUser())
            ->addRole($employeeRole);
    }

    public function testValidEntity(): void
    {
        /** @var RestaurantRoleRepository $userRoleRepository */
        $userRoleRepository = static::getContainer()->get(RestaurantRoleRepository::class);
        $employeeRole = $userRoleRepository->findOneBy(['role' => RestaurantRole::ADMIN]);
        $employee = UserTest::createValidUser();
        $restaurant = RestaurantTest::createValidRestaurant();

        $restaurantEmployee = (new RestaurantEmployee())
            ->setEmployee($employee)
            ->setRestaurant($restaurant)
            ->addRole($employeeRole);

        $this->assertSame($employee, $restaurantEmployee->getEmployee());
        $this->assertSame($restaurant, $restaurantEmployee->getRestaurant());
        $this->assertContains($employeeRole, $restaurantEmployee->getRoles());
    }
}
