<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserRole;
use App\Repository\UserRoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public const USER_GROUP = 'user';
    public const ADMIN_USER = 'ADMIN_USER';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRoleRepository $roleRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var UserRole $userRole */
        $userRole = $this->roleRepository->findOneBy(['role' => UserRole::USER]);
        $testUser = $this->createTestUser($userRole);
        $manager->persist($testUser);
        $adminUser = $this->createAdminUser();
        $manager->persist($adminUser);

        $password = $this->passwordHasher->hashPassword(new User(), 'zaq1@WSX');
        for ($i = 1; $i <= 50; ++$i) {
            $user = (new User())
                ->setEmail("user$i@test.com")
                ->setFirstName("User$i")
                ->setLastName('Test')
                ->setPassword($password)
                ->addRole($userRole);

            $manager->persist($user);
        }

        $manager->flush();
        $this->addReference(UserFixtures::ADMIN_USER, $adminUser);
    }

    public static function getGroups(): array
    {
        return [UserFixtures::USER_GROUP];
    }

    private function createTestUser(UserRole $userRole): User
    {
        $password = $this->passwordHasher->hashPassword(new User(), 'zaq1@WSX');

        return (new User())
            ->setEmail('test@test.com')
            ->setFirstName('Test')
            ->setLastName('Test')
            ->setPassword($password)
            ->addRole($userRole);
    }

    private function createAdminUser(): User
    {
        /** @var UserRole $adminRole */
        $adminRole = $this->roleRepository->findOneBy(['role' => UserRole::ADMIN]);
        $password = $this->passwordHasher->hashPassword(new User(), 'xsw2!QAZ');

        return (new User())
            ->setEmail('admin@admin.com')
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setPassword($password)
            ->addRole($adminRole);
    }
}
