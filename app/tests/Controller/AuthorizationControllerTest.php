<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Entity\UserTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;

/**
 * @property KernelBrowser $client
 */
class AuthorizationControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testRedirectFromLoginForm(): void
    {
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($user);
        $doctrine->flush();

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, '/auth/login');
        $this->assertResponseRedirects('/');
    }

    public function testLoginForm(): void
    {
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($user);
        $doctrine->flush();

        $crawler = $this->client->request(Request::METHOD_GET, '/auth/login');
        $form = $crawler->selectButton('send')->form([
            'email' => $user->getEmail(),
            'password' => $user->getPlainPassword(),
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('http://localhost/');

        /** @var UsageTrackingTokenStorage $token */
        $token = $this->getContainer()->get('security.token_storage');
        $user->eraseCredentials();
        /** @var User|null $loggedUser */
        $loggedUser = $token->getToken()?->getUser();
        $this->assertEquals($user->getId(), $loggedUser?->getId());
    }

    public function testLogoutRedirect(): void
    {
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($user);
        $doctrine->flush();

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, '/auth/logout');
        $this->assertResponseRedirects('http://localhost/auth/login');
    }
}
