<?php
namespace stoykov\Ohrana\Repositories;

use stoykov\Ohrana\Models\Doctrine\Role;

interface Roles
{
    /**
     * Returns all the permissions in the data store
     * @return array                Array of Permission models
     */
    public function getAllRoles();

    /**
     * Returns permissions for a given role
     * @param  int      $roleId     The id of the role you want to fetch permissions for
     * @return array                Array of Permission models
     */
    public function getRoles($userId);

    /**
     * Adds a list of permissions to the data store
     * @param array     $permissions Array of permission models
     */
    public function addRoles(array $roles);

    /**
     * Adds a permission to the data store
     * @param Permission $permission The permission model
     */
    public function addRole(Role $role);
}