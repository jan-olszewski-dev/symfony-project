<?php

namespace App\Security\Voter;

use App\Entity\Restaurant;
use App\Entity\RestaurantEmployee;
use App\Entity\RestaurantRole;
use App\Entity\User;
use App\Entity\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RestaurantAdminVoter extends Voter
{
    public const RESTAURANT_ADMIN = 'RESTAURANT_ADMIN';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::RESTAURANT_ADMIN == $attribute && $subject instanceof Restaurant;
    }

    /**
     * @param Restaurant $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (in_array(UserRole::ADMIN, $user->getRoles())) {
            return true;
        }

        return $subject->getEmployees()
            ->filter(function (RestaurantEmployee $permission) use ($user) {
                if ($permission->getEmployee()->getId() !== $user->getId()) {
                    return false;
                }

                return $permission->getRoles()->filter(function (RestaurantRole $role) {
                    return RestaurantRole::ADMIN === $role->getRole();
                })->isEmpty();
            })->isEmpty();
    }
}
