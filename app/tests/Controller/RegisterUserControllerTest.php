<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Entity\UserTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @property KernelBrowser $client
 */
class RegisterUserControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testRedirectLoggedUser(): void
    {
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($user);
        $doctrine->flush();

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, '/register');
        $this->assertResponseRedirects('/');
    }

    public function testRegisterForm(): void
    {
        $email = uniqid('email_').'@test.com';
        $plainPassword = 'zaq1@WSX';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $crawler = $this->client->request(Request::METHOD_GET, '/register');
        $form = $crawler->selectButton('Sign up')->form([
            'register_user' => [
                'email' => $email,
                'plainPassword' => [
                    'first' => $plainPassword,
                    'second' => $plainPassword,
                ],
                'firstName' => $firstName,
                'lastName' => $lastName,
            ],
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/auth/login');

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepository */
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->getId());
        $this->assertSame($email, $user->getEmail());
        $this->assertTrue($passwordHasher->isPasswordValid($user, $plainPassword));
        $this->assertSame($firstName, $user->getFirstName());
        $this->assertSame($lastName, $user->getLastName());
    }
}
