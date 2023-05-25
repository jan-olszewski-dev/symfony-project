<?php

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NotFoundRedirect
{
    public function __construct(public string $path, public string $scope)
    {
    }
}
