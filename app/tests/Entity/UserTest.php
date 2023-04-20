<?php

namespace App\Tests\Entity;

use App\Entity\User as EntityUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserTest extends KernelTestCase
{
    public static function createValidUser(): EntityUser
    {
        $user = (new EntityUser())
            ->setEmail(uniqid('email_') . '@test.com')
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

        return $user;
    }

    public function testValidUserEntity(): void
    {
        $email = uniqid('email_') . '@test.com';
        $firstName = uniqid('firstName');
        $lastName = uniqid('lastName');
        $plainPassword = 'zaq1@WSX';
        $googleSubId = uniqid('googleSubId_');
        $linkedInSubId = uniqid('linkedInSubId_');
        $facebookSubId = uniqid('facebookSubId_');
        $user = (new EntityUser())
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
}
