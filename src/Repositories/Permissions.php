<?php
namespace stoykov\Ohrana\Repositories;

use stoykov\Ohrana\Models\Doctrine\Permission;

interface Permissions
{
    /**
     * Returns all the permissions in the data store
     * @return array                Array of Permission models
     */
    public function getAllPermissions();

    /**
     * Returns permissions for a given role
     * @param  int      $roleId     The id of the role you want to fetch permissions for
     * @return array                Array of Permission models
     */
    public function getPermissions($roleId);

    /**
     * Adds a list of permissions to the data store
     * @param array     $permissions Array of permission models
     */
    public function addPermissions(array $permissions);

    /**
     * Adds a permission to the data store
     * @param Permission $permission The permission model
     */
    public function addPermission(Permission $permission);
}