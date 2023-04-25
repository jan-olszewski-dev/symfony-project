<?php

namespace App\Tests\Entity;

use App\Entity\Premises;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PremisesTest extends KernelTestCase
{
    public static function createValidPremises(): Premises
    {
        return (new Premises())
            ->setName(uniqid('name'))
            ->setRestaurant(RestaurantTest::createValidRestaurant())
            ->setAddress(AddressTest::createValidAddress());
    }

    public function testValidPremisesEntity(): void
    {
        $name = uniqid('name');
        $restaurant = RestaurantTest::createValidRestaurant();

        $premises = (new Premises())
            ->setName($name)
            ->setRestaurant($restaurant);

        $this->assertSame($name, $premises->getName());
        $this->assertSame($restaurant, $premises->getRestaurant());
    }
}
