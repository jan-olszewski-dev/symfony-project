<?php

namespace App\Security;

use App\Entity\UserRole;
use Symfony\Component\Security\Core\Role\RoleHierarchy as SecurityRoleHierarchy;

class RoleHierarchy extends SecurityRoleHierarchy
{
    /**
     * @param array<UserRole> $roles
     */
    public function getReachableRoleNames(array $roles): array
    {
        $rolesAsString = array_map(function (UserRole $role) {
            return (string) $role;
        }, $roles);

        return parent::getReachableRoleNames($rolesAsString);
    }
}
