<?php

namespace App\Tests\Entity;

use App\Entity\Premises;
use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestaurantTest extends KernelTestCase
{
    public static function createValidRestaurant(): Restaurant
    {
        return (new Restaurant())
            ->setName(uniqid('name'))
            ->addEmployee(RestaurantEmployeeTest::createValidRestaurantEmployee());
    }

    public function testValidRestaurantEntity(): void
    {
        $name = uniqid('name');
        $premises = (new Premises())
            ->setName(uniqid('name'))
            ->setAddress(AddressTest::createValidAddress());

        $restaurant = (new Restaurant())
            ->setName($name)
            ->addPremise($premises);

        $this->assertSame($name, $restaurant->getName());
        $this->assertTrue($restaurant->getPremises()->contains($premises));
    }
}
