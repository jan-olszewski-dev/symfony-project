<?php

namespace App\Tests\Controller;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Premises;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\RestaurantRepository;
use App\Tests\Entity\RestaurantTest;
use App\Tests\Entity\UserTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property KernelBrowser $client
 */
class RestaurantControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testRestaurantListAsNotLoggedUser(): void
    {
        $restaurant = RestaurantTest::createValidRestaurant();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($restaurant);
        $doctrine->flush();

        $this->client->request(Request::METHOD_GET, '/restaurant/search');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('.restaurants-wrapper h2', $restaurant->getName());
        $this->assertSelectorNotExists('.restaurants-wrapper a');
    }

    public function testRestaurantListAsRestaurantAdminUser(): void
    {
        $restaurant = RestaurantTest::createValidRestaurant();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($restaurant);
        $doctrine->flush();
        /** @var User $user */
        $user = $restaurant->getEmployees()->get(0)?->getEmployee();
        $this->client->loginUser($user);

        $this->client->request(Request::METHOD_GET, '/restaurant/search');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('.restaurants-wrapper h2', $restaurant->getName());
        $this->client->clickLink('more info');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('.restaurant-wrapper h2', $restaurant->getName());
        $this->assertSelectorTextSame('.restaurant-wrapper p', 'No premises assigned to restaurant');
    }

    public function testRestaurantInfoAsNormalUser()
    {
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($user);
        $doctrine->flush();
        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, '/restaurant/99');
        $this->assertResponseRedirects('/restaurant/search');
    }

    public function testCreateRestaurant(): void
    {
        $restaurantName = uniqid('name');
        $localName = uniqid('name');
        $street = uniqid('street');
        $streetNumber = (string) rand(0, 100);
        $flatNumber = (string) rand(0, 1000);
        $postalCode = (string) rand(10000, 99999);
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var City $city */
        $city = $doctrine->getRepository(City::class)->find(1);
        $doctrine->persist($user);
        $doctrine->flush();

        $this->client->loginUser($user);
        $crawler = $this->client->request(Request::METHOD_GET, '/restaurant/create');
        $form = $crawler->filter('.form-wrapper input[type="submit"]')->form([
            'create_restaurant' => [
                'restaurant' => [
                    'name' => $restaurantName,
                ],
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
        $this->assertResponseRedirects('/restaurant/search');

        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var RestaurantRepository $restaurantRepository */
        $restaurantRepository = $doctrine->getRepository(Premises::class);
        $premises = $restaurantRepository->findOneBy(['name' => $localName]);

        $this->assertInstanceOf(Premises::class, $premises);
        $this->assertNotEmpty($premises->getId());
        $this->assertSame($localName, $premises->getName());

        $restaurant = $premises->getRestaurant();
        $this->assertInstanceOf(Restaurant::class, $restaurant);
        $this->assertNotEmpty($restaurant->getId());
        $this->assertSame($restaurantName, $restaurant->getName());

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
