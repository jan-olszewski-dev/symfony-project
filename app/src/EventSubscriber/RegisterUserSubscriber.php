<?php

namespace App\EventSubscriber;

use App\Entity\UserRole;
use App\Event\RegisterUserEvent;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @SuppressWarnings(PHPMD.MissingImport) */
class RegisterUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRoleRepository $userRoleRepository
    ) {
    }

    public function checkIfUserExists(RegisterUserEvent $event): void
    {
        $user = $event->getUser();
        $registeredUser = $this->repository->findOneBy(['email' => $user->getEmail()]);

        if ($registeredUser) {
            $event->stopPropagation();
        }
    }

    public function onRegisterUserEventPost(RegisterUserEvent $event): void
    {
        if (!$event->isPropagationStopped()) {
            /** @var UserRole $userRole */
            $userRole = $this->userRoleRepository->findOneBy(['role' => UserRole::USER]);
            $user = $event->getUser()->addRole($userRole);
            $user->eraseCredentials();
            $this->repository->save($user)->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegisterUserEvent::REGISTER_USER => [
                ['checkIfUserExists', 10],
                ['onRegisterUserEventPost', -10],
            ],
            RegisterUserEvent::REGISTER_SOCIAL_USER => [
                ['checkIfUserExists', 10],
                ['onRegisterUserEventPost', -10],
            ],
        ];
    }
}
