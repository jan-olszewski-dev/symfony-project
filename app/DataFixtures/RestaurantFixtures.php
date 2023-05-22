<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RestaurantFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const RESTAURANT_GROUP = 'restaurant';

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createMcDonald());
        $manager->persist($this->createKFC());
        $manager->persist($this->createBurgerKing());

        for ($i = 0; $i <= 30; ++$i) {
            $restaurant = (new Restaurant())->setName(uniqid('Restaurant_'));
            $manager->persist($restaurant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    public static function getGroups(): array
    {
        return [RestaurantFixtures::RESTAURANT_GROUP];
    }

    private function createMcDonald(): Restaurant
    {
        return (new Restaurant())
            ->setName("Mc Donald's");
    }

    private function createKFC(): Restaurant
    {
        return (new Restaurant())
            ->setName('KFC');
    }

    private function createBurgerKing(): Restaurant
    {
        return (new Restaurant())
            ->setName('Burger King');
    }
}
