<?php

namespace App\Tests\Entity;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestaurantTest extends KernelTestCase
{
    public static function createValidRestaurant(): Restaurant
    {
        return (new Restaurant())
            ->setName(uniqid('name'));
    }

    public function testValidRestaurantEntity(): void
    {
        $name = uniqid('name');
        $restaurant = (new Restaurant())
            ->setName($name);

        $this->assertSame($name, $restaurant->getName());
    }
}
