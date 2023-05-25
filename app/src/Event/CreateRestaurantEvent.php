<?php

namespace App\Event;

use App\Entity\Premises;
use App\Entity\Restaurant;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateRestaurantEvent extends Event
{
    public const NAME = 'create.restaurant.event';

    public function __construct(private readonly Premises $premises)
    {
    }

    public function getPremises(): Premises
    {
        return $this->premises;
    }

    public function getRestaurant(): Restaurant
    {
        return $this->premises->getRestaurant();
    }
}
