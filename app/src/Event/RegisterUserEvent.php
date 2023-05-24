<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class RegisterUserEvent extends Event
{
    public const REGISTER_USER = 'register.user.event';
    public const REGISTER_SOCIAL_USER = 'register.social.user.event';

    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
