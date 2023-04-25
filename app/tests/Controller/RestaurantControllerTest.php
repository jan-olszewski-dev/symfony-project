<?php

namespace App\Tests\Controller;

use App\Tests\Entity\RestaurantTest;
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

    public function testRestaurants(): void
    {
        $restaurant = RestaurantTest::createValidRestaurant();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($restaurant);
        $doctrine->flush();

        $this->client->request(Request::METHOD_GET, '/restaurant');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h2', $restaurant->getName());
        $this->client->clickLink('more info');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h2', $restaurant->getName());
        $this->assertSelectorTextSame('p', 'No premises for restaurant');
    }

    public function testNotFoundRestaurantRedirect(): void
    {
        $this->client->request(Request::METHOD_GET, '/restaurant/1');
        $this->assertResponseRedirects('/restaurant');
    }
}
