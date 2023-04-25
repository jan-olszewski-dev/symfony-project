<?php

namespace App\Tests\Entity;

use App\Entity\Address;
use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddressTest extends KernelTestCase
{
    public static function createValidAddress(): Address
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        /** @var City $city */
        $city = $entityManager->getRepository(City::class)->find(1);

        return (new Address())
            ->setStreet(uniqid('street'))
            ->setStreetNumber((string) rand(1, 100))
            ->setFlatNumber((string) rand(1, 1000))
            ->setPostalCode((string) rand(10000, 99999))
            ->setCity($city);
    }

    public function testValidAddressEntity(): void
    {
        $street = uniqid('street');
        $streetNumber = (string) rand(1, 100);
        $flatNumber = (string) rand(1, 1000);
        $postalCode = (string) rand(10000, 99999);
        $city = (new City())->setName(uniqid('city'));

        $address = (new Address())
            ->setStreet($street)
            ->setStreetNumber($streetNumber)
            ->setFlatNumber($flatNumber)
            ->setPostalCode($postalCode)
            ->setCity($city);

        $this->assertSame($street, $address->getStreet());
        $this->assertSame($streetNumber, $address->getStreetNumber());
        $this->assertSame($flatNumber, $address->getFlatNumber());
        $this->assertSame($postalCode, $address->getPostalCode());
        $this->assertSame($city, $address->getCity());
    }
}
