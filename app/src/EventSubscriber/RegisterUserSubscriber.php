<?php

namespace App\EventSubscriber;

use App\Event\RegisterUserEvent;
use App\Repository\UserRepository;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository              $repository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function onRegisterUserEventPre(RegisterUserEvent $event): void
    {
        $user = $event->getUser();
        $password = $this->passwordHasher->hashPassword($user, (string)$user->getPlainPassword());
        $user->setPassword($password);
    }

    public function onRegisterUserEventPost(RegisterUserEvent $event): void
    {
        $user = $event->getUser();

        if (!$this->passwordHasher->isPasswordValid($user, (string)$user->getPlainPassword())) {
            throw new LogicException('Password don\'t match');
        }

        $user->eraseCredentials();
        $this->repository
            ->save($user)
            ->flush();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegisterUserEvent::NAME => [
                ['onRegisterUserEventPre', 10],
                ['onRegisterUserEventPost', -10],
            ],
        ];
    }
}
