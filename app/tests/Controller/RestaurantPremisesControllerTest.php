<?php

namespace App\Tests\Controller;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Premises;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\RestaurantRepository;
use App\Tests\Entity\RestaurantTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property KernelBrowser $client
 */
class RestaurantPremisesControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testAddPremises(): void
    {
        $restaurant = RestaurantTest::createValidRestaurant();
        $localName = uniqid('name');
        $street = uniqid('street');
        $streetNumber = (string) rand(0, 100);
        $flatNumber = (string) rand(0, 1000);
        $postalCode = (string) rand(10000, 99999);
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var City $city */
        $city = $doctrine->getRepository(City::class)->find(1);
        $doctrine->persist($restaurant);
        $doctrine->flush();
        /** @var User $user */
        $user = $restaurant->getEmployees()->get(0)?->getEmployee();
        $this->client->loginUser($user);
        $crawler = $this->client->request(Request::METHOD_GET, "/premises/{$restaurant->getId()}/add");
        $form = $crawler->selectButton('Create')->form([
            'premises' => [
                'name' => $localName,
                'address' => [
                    'street' => $street,
                    'streetNumber' => $streetNumber,
                    'flatNumber' => $flatNumber,
                    'postalCode' => $postalCode,
                    'city' => $city->getId(),
                ],
            ],
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects("/restaurant/{$restaurant->getId()}");

        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var RestaurantRepository $restaurantRepository */
        $restaurantRepository = $doctrine->getRepository(Premises::class);
        $premises = $restaurantRepository->findOneBy(['name' => $localName]);

        $this->assertInstanceOf(Premises::class, $premises);
        $this->assertNotEmpty($premises->getId());
        $this->assertSame($localName, $premises->getName());
        $this->assertInstanceOf(Restaurant::class, $premises->getRestaurant());
        $this->assertSame($restaurant->getId(), $premises->getRestaurant()->getId());

        $address = $premises->getAddress();
        $this->assertInstanceOf(Address::class, $address);
        $this->assertNotEmpty($address->getId());
        $this->assertSame($street, $address->getStreet());
        $this->assertSame($streetNumber, $address->getStreetNumber());
        $this->assertSame($flatNumber, $address->getFlatNumber());
        $this->assertSame($postalCode, $address->getPostalCode());
        $this->assertEquals($city, $address->getCity());
    }
}
