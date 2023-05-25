<?php

namespace App\Tests\Controller;

use App\Entity\RestaurantEmployee;
use App\Entity\RestaurantRole;
use App\Entity\User;
use App\Tests\Entity\RestaurantTest;
use App\Tests\Entity\UserTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property KernelBrowser $client
 */
class RestaurantEmployeeControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testAddRestaurantEmployee(): void
    {
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $restaurant = RestaurantTest::createValidRestaurant();
        $email = uniqid('email_').'@test.com';
        $plainPassword = 'zaq1@WSX';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $restaurantRole = $doctrine->getRepository(RestaurantRole::class)->find(1);
        $doctrine->persist($restaurant);
        $doctrine->flush();

        $crawler = $this->client->request(Request::METHOD_GET, "/restaurant/{$restaurant->getId()}/employee/add");
        $form = $crawler->filter('.form-wrapper input[type="submit"]')->form([
            'employee[employee]' => [
                'email' => $email,
                'plainPassword' => [
                    'first' => $plainPassword,
                    'second' => $plainPassword,
                ],
                'firstName' => $firstName,
                'lastName' => $lastName,
            ],
            'employee[roles]' => [$restaurantRole->getId()],
        ]);

        $this->client->submit($form);
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);
        $this->assertResponseRedirects("/restaurant/{$restaurant->getId()}/employee/edit/{$user->getId()}");

        /** @var RestaurantEmployee $employee */
        $employee = $doctrine
            ->createQuery('SELECT re FROM '.RestaurantEmployee::class.' re WHERE re.employee = :user')
            ->setParameter('user', $user)
            ->setFetchMode(RestaurantEmployee::class, 'roles', 'EAGER')
            ->setFetchMode(RestaurantEmployee::class, 'employee', 'EAGER')
            ->setFetchMode(RestaurantEmployee::class, 'restaurant', 'EAGER')
            ->getOneOrNullResult();

        $this->assertSame($user->getId(), $employee->getEmployee()->getId());
        $this->assertSame($email, $employee->getEmployee()->getEmail());
        $this->assertSame($firstName, $employee->getEmployee()->getFirstName());
        $this->assertSame($lastName, $employee->getEmployee()->getLastName());
        $this->assertNotEmpty($employee->getEmployee()->getPassword());
        $this->assertSame($restaurant->getId(), $employee->getRestaurant()->getId());
        $this->assertContainsEquals($restaurantRole, $employee->getRoles());
    }

    public function testAddExistingRestaurantEmployee(): void
    {
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $restaurant = RestaurantTest::createValidRestaurant();
        $user = UserTest::createValidUser();
        $restaurantRole = $doctrine->getRepository(RestaurantRole::class)->find(1);
        $doctrine->persist($restaurant);
        $doctrine->persist($user);
        $doctrine->flush();

        $crawler = $this->client->request(Request::METHOD_GET, "/restaurant/{$restaurant->getId()}/employee/add");
        $form = $crawler->filter('.form-wrapper input[type="submit"]')->form([
            'employee[employee]' => [
                'email' => $user->getEmail(),
                'plainPassword' => [
                    'first' => $user->getPlainPassword(),
                    'second' => $user->getPlainPassword(),
                ],
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ],
            'employee[roles]' => [$restaurantRole->getId()],
        ]);

        $this->client->submit($form);
        $this->assertResponseIsUnprocessable();
    }

    public function testEditRestaurantEmployee(): void
    {
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $restaurant = RestaurantTest::createValidRestaurant();
        $email = uniqid('email_').'@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $restaurantRole = $doctrine->getRepository(RestaurantRole::class)->find(1);
        $doctrine->persist($restaurant);
        $doctrine->flush();
        $user = $restaurant->getEmployees()->get(0)->getEmployee();

        $crawler = $this->client->request(Request::METHOD_GET, "/restaurant/{$restaurant->getId()}/employee/edit/{$user->getId()}");

        $form = $crawler->filter('.form-wrapper input[type="submit"]')->form([
            'employee[employee]' => [
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
            ],
            'employee[roles]' => [$restaurantRole->getId()],
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects("/restaurant/{$restaurant->getId()}/employee/edit/{$user->getId()}");

        /** @var RestaurantEmployee $employee */
        $employee = $doctrine
            ->createQuery('SELECT re FROM '.RestaurantEmployee::class.' re WHERE re.employee = :user')
            ->setParameter('user', $user)
            ->setFetchMode(RestaurantEmployee::class, 'roles', 'EAGER')
            ->setFetchMode(RestaurantEmployee::class, 'employee', 'EAGER')
            ->setFetchMode(RestaurantEmployee::class, 'restaurant', 'EAGER')
            ->getOneOrNullResult();

        $this->assertSame($user->getId(), $employee->getEmployee()->getId());
        $this->assertSame($email, $employee->getEmployee()->getEmail());
        $this->assertSame($firstName, $employee->getEmployee()->getFirstName());
        $this->assertSame($lastName, $employee->getEmployee()->getLastName());
        $this->assertNotEmpty($employee->getEmployee()->getPassword());
        $this->assertSame($restaurant->getId(), $employee->getRestaurant()->getId());
        $this->assertContainsEquals($restaurantRole, $employee->getRoles());
    }

    public function testDeleteRestaurantEmployee(): void
    {
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $restaurant = RestaurantTest::createValidRestaurant();
        $doctrine->persist($restaurant);
        $doctrine->flush();
        $user = $restaurant->getEmployees()->get(0)->getEmployee();

        $this->client->request(Request::METHOD_GET, "/restaurant/{$restaurant->getId()}/employee/remove/{$user->getId()}");
        $this->assertResponseRedirects("/restaurant/{$restaurant->getId()}");
    }
}
