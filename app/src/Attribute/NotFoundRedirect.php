<?php

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD, \Attribute::TARGET_CLASS)]
class NotFoundRedirect
{
    public function __construct(public string $path, public string $scope)
    {
    }
}
