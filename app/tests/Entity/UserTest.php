<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\UserRole;
use App\Repository\UserRoleRepository;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends KernelTestCase
{
    public static function createValidUser(): User
    {
        $user = (new User())
            ->setEmail(uniqid('email_').'@test.com')
            ->setFirstName(uniqid('firstName'))
            ->setLastName(uniqid('lastName'))
            ->setPlainPassword('zaq1@WSX')
            ->setGoogleSubId(uniqid('googleSubId_'))
            ->setLinkedInSubId(uniqid('linkedInSubId_'))
            ->setFacebookSubId(uniqid('facebookSubId_'));

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $password = $hasher->hashPassword($user, 'zaq1@WSX');
        $user->setPassword($password);

        /** @var UserRoleRepository $userRoleRepository */
        $userRoleRepository = static::getContainer()->get(UserRoleRepository::class);
        /** @var UserRole $userRole */
        $userRole = $userRoleRepository->findOneBy(['role' => UserRole::USER]);
        $user->addRole($userRole);

        return $user;
    }

    public function testValidUserEntity(): void
    {
        $email = uniqid('email_').'@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $plainPassword = 'zaq1@WSX';
        $googleSubId = uniqid('googleSubId_');
        $linkedInSubId = uniqid('linkedInSubId_');
        $facebookSubId = uniqid('facebookSubId_');
        $user = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPlainPassword($plainPassword)
            ->setGoogleSubId($googleSubId)
            ->setLinkedInSubId($linkedInSubId)
            ->setFacebookSubId($facebookSubId);

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $password = $hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);

        $this->assertSame($email, $user->getEmail());
        $this->assertSame($firstName, $user->getFirstName());
        $this->assertSame($lastName, $user->getLastName());
        $this->assertSame($plainPassword, $user->getPlainPassword());
        $this->assertSame($password, $user->getPassword());
        $this->assertSame($googleSubId, $user->getGoogleSubId());
        $this->assertSame($linkedInSubId, $user->getLinkedInSubId());
        $this->assertSame($facebookSubId, $user->getFacebookSubId());
    }

    public function testCreateGoogleUser(): void
    {
        $email = uniqid('email_').'@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $googleSubId = uniqid('googleSubId_');
        $expectedUser = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setGoogleSubId($googleSubId);

        $googleUser = new GoogleUser([
            'sub' => $googleSubId,
            'given_name' => $firstName,
            'family_name' => $lastName,
            'email' => $email,
        ]);

        $this->assertEquals($expectedUser, User::createGoogleUser($googleUser));
    }

    public function testCreateLinkedInUser(): void
    {
        $email = uniqid('email_').'@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $linkedInSubId = uniqid('linkedInSubId_');
        $expectedUser = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setLinkedInSubId($linkedInSubId);

        $linkedInUser = new LinkedInResourceOwner([
            'localizedLastName' => $lastName,
            'id' => $linkedInSubId,
            'localizedFirstName' => $firstName,
            'email' => $email,
        ]);

        $this->assertEquals($expectedUser, User::createLinkedInUser($linkedInUser));
    }

    public function testCreateFacebookUser(): void
    {
        $email = uniqid('email_').'@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $facebookSubId = uniqid('facebookSubId_');
        $expectedUser = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setFacebookSubId($facebookSubId);

        $facebookUser = new FacebookUser([
            'id' => $facebookSubId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ]);

        $this->assertEquals($expectedUser, User::createFacebookUser($facebookUser));
    }
}
