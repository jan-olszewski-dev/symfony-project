<?php

namespace App\Event\RegisterUser;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class RegisterUserEvent extends Event
{
    const NAME = 'register.user.event';

    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
