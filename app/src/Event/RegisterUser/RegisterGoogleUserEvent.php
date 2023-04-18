<?php

namespace App\Event\RegisterUser;

use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Contracts\EventDispatcher\Event;

class RegisterGoogleUserEvent extends Event implements RegisterSocialUserEvent
{
    public function __construct(private GoogleUser $user)
    {
    }

    public function getUser(): GoogleUser
    {
        return $this->user;
    }
}
