<?php
namespace stoykov\Ohrana\Repositories;

use stoykov\Ohrana\Models\Doctrine\Permission AS PermissionModel;

class DoctrinePermissions implements Permissions
{
    /**
     * Returns all the permissions in the data store
     * @return array                Array of Permission models
     */
    public function getAllPermissions()
    {
        return \EntityManager::getRepository('stoykov\Ohrana\Models\Doctrine\Permission')->findAll();
    }

    /**
     * Returns permissions for a given role
     * @param  int      $roleId     The id of the role you want to fetch permissions for
     * @return array                Array of Permission models
     */
    public function getPermissions($roleId)
    {
        return \EntityManager::getRepository('stoykov\Ohrana\Models\Doctrine\Permission')->findBy(['roleId' => $roleId]);
    }

    /**
     * Adds a list of permissions to the data store
     * @param array     $permissions Array of permission models
     */
    public function addPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            \EntityManager::persist($permission);
        }
        \EntityManager::flush(); //Persist objects
        \EntityManager::clear(); // Detaches all objects from Doctrine!
    }

    /**
     * Adds a permission to the data store
     * @param stoykov\Ohrana\Models\Doctrine\Permission $permission The permission model
     */
    public function addPermission(PermissionModel $permission)
    {
        EntityManager::persist($permission);
        EntityManager::flush();
    }
}