<?php

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class NotFoundRedirect
{
    public function __construct(public string $path, public string $scope)
    {
    }
}
