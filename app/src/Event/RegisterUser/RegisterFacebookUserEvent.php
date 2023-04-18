<?php

namespace App\Event\RegisterUser;

use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Contracts\EventDispatcher\Event;

class RegisterFacebookUserEvent extends Event implements RegisterSocialUserEvent
{
    public function __construct(private FacebookUser $user)
    {
    }

    public function getUser(): FacebookUser
    {
        return $this->user;
    }
}
