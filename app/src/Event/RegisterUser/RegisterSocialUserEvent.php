<?php

namespace App\Event\RegisterUser;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Psr\EventDispatcher\StoppableEventInterface;

interface RegisterSocialUserEvent extends StoppableEventInterface
{
    const NAME = 'register.social.user.event';

    public function getUser(): ResourceOwnerInterface;

    public function stopPropagation(): void;
}
