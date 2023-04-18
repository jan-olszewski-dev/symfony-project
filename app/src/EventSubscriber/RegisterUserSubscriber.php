<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\RegisterUser\RegisterGoogleUserEvent;
use App\Event\RegisterUser\RegisterSocialUserEvent;
use App\Event\RegisterUser\RegisterUserEvent;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterUserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository              $repository,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface          $validator
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

    public function onRegisterSocialUserEventPre(RegisterSocialUserEvent $event): void
    {
        $socialUser = $event->getUser();
        $user = match ($event::class) {
            RegisterGoogleUserEvent::class => $this->repository->findOneBy(['googleSubId' => $socialUser->getId()]),
            default => null,
        };

        if ($user) {
            $event->stopPropagation();
        }
    }

    public function onRegisterSocialUserEventPost(RegisterSocialUserEvent $event): void
    {
        if (!$event->isPropagationStopped()) {
            $user = match ($event::class) {
                RegisterGoogleUserEvent::class => $this->getGoogleUser($event->getUser()),
                default => throw new LogicException('Unsupported social account registration'),
            };
            $this->validator->validate($user);
            $this->repository
                ->save($user)
                ->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RegisterUserEvent::NAME => [
                ['onRegisterUserEventPre', 10],
                ['onRegisterUserEventPost', -10],
            ],
            RegisterSocialUserEvent::NAME => [
                ['onRegisterSocialUserEventPre', 10],
                ['onRegisterSocialUserEventPost', -10],
            ],
        ];
    }

    private function getGoogleUser(GoogleUser $googleUser): User
    {
        $user = $this->repository->findOneBy(['email' => $googleUser->getEmail()]);

        return $user ?
            $user->setGoogleSubId($googleUser->getId()) :
            User::createGoogleUser($googleUser);
    }
}
