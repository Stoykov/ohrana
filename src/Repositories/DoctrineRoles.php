<?php
namespace stoykov\Ohrana\Repositories;

use stoykov\Ohrana\Models\Doctrine\Role AS RoleModel;

class DoctrineRoles implements Roles
{
    /**
     * Returns all the permissions in the data store
     * @return array                Array of Permission models
     */
    public function getAllRoles()
    {
        return \EntityManager::getRepository('stoykov\Ohrana\Models\Doctrine\Role')->findAll();
    }

    /**
     * Returns permissions for a given role
     * @param  int      $userId     The id of the role you want to fetch permissions for
     * @return array                Array of Permission models
     */
    public function getRoles($userId)
    {
        $user = \EntityManager::getRepository(config('ohrana.user_model'))->findById($userId);
        return $user->getRoles(); //->first()->getName()
    }

    /**
     * Adds a list of permissions to the data store
     * @param int       $roleId      The id of the role you want to attach the permissions to
     * @param array     $permissions Array of permission models
     */
    public function addRoles(array $roles)
    {
        foreach ($roles as $role) {
            \EntityManager::persist($role);
        }
        \EntityManager::flush(); //Persist objects
        \EntityManager::clear(); // Detaches all objects from Doctrine!
    }

    /**
     * Adds a permission to the data store
     * @param stoykov\Ohrana\Models\Doctrine\Role $role The role model
     */
    public function addRole(RoleModel $role)
    {
        EntityManager::persist($role);
        EntityManager::flush();
    }
}