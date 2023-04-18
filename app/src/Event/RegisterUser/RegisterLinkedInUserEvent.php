<?php

namespace App\Event\RegisterUser;

use League\OAuth2\Client\Provider\LinkedInResourceOwner;
use Symfony\Contracts\EventDispatcher\Event;

class RegisterLinkedInUserEvent extends Event implements RegisterSocialUserEvent
{
    public function __construct(private LinkedInResourceOwner $user)
    {
    }

    public function getUser(): LinkedInResourceOwner
    {
        return $this->user;
    }
}
