<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserRole;
use App\Repository\UserRoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserRole $userRole;
    private UserRole $adminRole;

    public function __construct(private UserPasswordHasherInterface $passwordHasher, UserRoleRepository $roleRepository)
    {
        /** @var UserRole $userRole */
        $userRole = $roleRepository->findOneBy(['role' => UserRole::USER]);
        $this->userRole = $userRole;
        /** @var UserRole $adminRole */
        $adminRole = $roleRepository->findOneBy(['role' => UserRole::ADMIN]);
        $this->adminRole = $adminRole;
    }

    public function load(ObjectManager $manager): void
    {
        $testUser = $this->createTestUser();
        $manager->persist($testUser);
        $adminUser = $this->createAdminUser();
        $manager->persist($adminUser);
        $manager->flush();
    }

    private function createTestUser(): User
    {
        $password = $this->passwordHasher->hashPassword(new User(), 'zaq1@WSX');

        return (new User())
            ->setEmail('test@test.com')
            ->setFirstName('Test')
            ->setLastName('Test')
            ->setPassword($password)
            ->addRole($this->userRole);
    }

    private function createAdminUser(): User
    {
        $password = $this->passwordHasher->hashPassword(new User(), 'xsw2!QAZ');

        return (new User())
            ->setEmail('admin@admin.com')
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setPassword($password)
            ->addRole($this->adminRole);
    }
}
