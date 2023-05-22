<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Premises;
use App\Entity\Restaurant;
use App\Repository\CityRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RestaurantPremisesFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(private readonly CityRepository $cityRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var Restaurant $restaurant */
        foreach ($manager->getRepository(Restaurant::class)->findAll() as $restaurant) {
            foreach ($this->getLocales() as $premise) {
                $premise->setRestaurant($restaurant);
                $manager->persist($premise);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RestaurantFixtures::class];
    }

    public static function getGroups(): array
    {
        return [RestaurantFixtures::RESTAURANT_GROUP];
    }

    private function getLocales(): \Generator
    {
        for ($i = 0; $i <= 10; ++$i) {
            yield (new Premises())
                ->setName(uniqid('Local_'))
                ->setAddress($this->getAddress());
        }
    }

    private function getAddress(): Address
    {
        return (new Address())
            ->setStreet(uniqid('street'))
            ->setStreetNumber(rand(1, 100))
            ->setFlatNumber(rand(1, 1000))
            ->setPostalCode(str_pad(rand(1, 99999), 5, '0'))
            ->setCity($this->cityRepository->find(rand(1, 340)));
    }
}
