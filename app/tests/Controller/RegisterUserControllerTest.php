<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Entity\UserTest;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Client\Provider\LinkedInClient;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use Mockery;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
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

    public function testRedirectLoggedUser()
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

    public function testRegisterForm()
    {
        $email = uniqid('email_') . '@test.com';
        $plainPassword = 'zaq1@WSX';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $crawler = $this->client->request(Request::METHOD_GET, '/register');
        $form = $crawler->selectButton('Send')->form([
            'register_user' => [
                'email' => $email,
                'plainPassword' => [
                    'first' => $plainPassword,
                    'second' => $plainPassword
                ],
                'firstName' => $firstName,
                'lastName' => $lastName,
            ]
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

    public function testRegisterGoogleUser()
    {
        $subId = uniqid('sub_id');
        $email = uniqid('email_') . '@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $googleUser = new GoogleUser([
            'sub' => $subId,
            'given_name' => $firstName,
            'family_name' => $lastName,
            'email' => $email,
        ]);
        $mock = Mockery::mock(GoogleClient::class)->makePartial();
        $mock->shouldReceive('fetchUser')
            ->andReturn($googleUser);

        static::getContainer()->set(GoogleClient::class, $mock);
        $this->client->request(Request::METHOD_GET, '/register/google');
        $this->assertResponseRedirects('/');

        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepository */
        $userRepository = $doctrine->getRepository(User::class);
        /** @var Security $security */
        $security = static::getContainer()->get(Security::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($subId, $user->getGoogleSubId());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($firstName, $user->getFirstName());
        $this->assertSame($lastName, $user->getLastName());
        $this->assertSame($user, $security->getUser());
    }

    public function testRegisterLinkedInUser()
    {
        $subId = uniqid('sub_id');
        $email = uniqid('email_') . '@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $linkedInUser = new LinkedInResourceOwner([
            'id' => $subId,
            'email' => $email,
            'localizedFirstName' => $firstName,
            'localizedLastName' => $lastName,
        ]);
        $mock = Mockery::mock(LinkedInClient::class)->makePartial();
        $mock->shouldReceive('fetchUser')
            ->andReturn($linkedInUser);

        static::getContainer()->set(LinkedInClient::class, $mock);
        $this->client->request(Request::METHOD_GET, '/register/linkedin');
        $this->assertResponseRedirects('/');

        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepository */
        $userRepository = $doctrine->getRepository(User::class);
        /** @var Security $security */
        $security = static::getContainer()->get(Security::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($subId, $user->getLinkedInSubId());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($firstName, $user->getFirstName());
        $this->assertSame($lastName, $user->getLastName());
        $this->assertSame($user, $security->getUser());
    }

    public function testRegisterFacebookUser()
    {
        $subId = uniqid('sub_id');
        $email = uniqid('email_') . '@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $linkedInUser = new FacebookUser([
            'id' => $subId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);
        $mock = Mockery::mock(FacebookClient::class)->makePartial();
        $mock->shouldReceive('fetchUser')
            ->andReturn($linkedInUser);

        static::getContainer()->set(FacebookClient::class, $mock);
        $this->client->request(Request::METHOD_GET, '/register/facebook');
        $this->assertResponseRedirects('/');

        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepository */
        $userRepository = $doctrine->getRepository(User::class);
        /** @var Security $security */
        $security = static::getContainer()->get(Security::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($subId, $user->getFacebookSubId());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($firstName, $user->getFirstName());
        $this->assertSame($lastName, $user->getLastName());
        $this->assertSame($user, $security->getUser());
    }

    public function testRegisterAlreadyExistingSocialUser()
    {
        $user = UserTest::createValidUser();
        /** @var EntityManagerInterface $doctrine */
        $doctrine = static::getContainer()->get(EntityManagerInterface::class);
        $doctrine->persist($user);
        $doctrine->flush();
        $linkedInUser = new FacebookUser([
            'id' => $user->getFacebookSubId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
        ]);
        $mock = Mockery::mock(FacebookClient::class)->makePartial();
        $mock->shouldReceive('fetchUser')
            ->andReturn($linkedInUser);

        static::getContainer()->set(FacebookClient::class, $mock);
        $this->client->request(Request::METHOD_GET, '/register/facebook');
        $this->assertResponseRedirects('/');

        /** @var Security $security */
        $security = static::getContainer()->get(Security::class);
        $this->assertSame($user, $security->getUser());
    }
}
