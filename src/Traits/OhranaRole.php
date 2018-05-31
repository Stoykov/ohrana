<?php
namespace stoykov\Ohrana\Traits;

use stoykov\Ohrana\Models\Doctrine\Role;
use stoykov\Ohrana\Models\Doctrine\Permission;

use stoykov\Ohrana\Repositories\Permissions AS PermissionRepo;
use stoykov\Ohrana\Repositories\Roles AS RoleRepo;

use stoykov\Ohrana\Exceptions\NoRepositoryException;

trait OhranaRole
{
    use OhranaHelper;

    /**
     * Role
     * @var array
     */
    protected $roles = null;

    /**
     * An array of all the permissions
     * @var array
     */
    protected $permissions = null;

    /**
     * Permissions repository
     * @var stoykov\Ohrana\Repositories\Permission
     */
    protected $permissionRepository;

    /**
     * Role repository
     * @var stoykov\Ohrana\Repositories\Role
     */
    protected $roleRepository;

    /**
     * Does this role have a global permission?
     * @var boolean
     */
    protected $isGlobal = false;

    /**
     * Get the role of the user
     */
    protected function getRoles()
    {
        $this->roles = $this->roleRepository->getRole($this->id)->toArray();
    }

    /**
     * Set the permissions repository
     * @param PermissionRepo $repository
     */
    public function setPermissionRepository(PermissionRepo $repository = null)
    {
        if ($repository) {
            $this->permissionRepository = $repository;
        } else {
            $repository = config('ohrana.repositories.permissions');
            $this->permissionRepository = new $repository;
        }
    }

    /**
     * Set the permissions repository
     * @param RoleRepo $repository
     */
    public function setRoleRepository(RoleRepo $repository = null)
    {
        if ($repository) {
            $this->roleRepository = $repository;
        } else {
            $repository = config('ohrana.repositories.roles');
            $this->roleRepository = new $repository;
        }
    }

    /**
     * Check if user has permissions to perform the given action
     * @param  string  $action Action fetched from Lumen's router (example: App\Http\Controllers\ExampleController@test)
     * @return boolean
     */
    public function hasPermisson($action)
    {
        $this->setRoleRepository(); // Load up the roles repository
        $this->setPermissionRepository(); // Load up permissions repository

        if (!$this->roles)
            $this->getRoles(); // Get all the user's attached roles

        if (!$this->permissions)
            $this->getPermissions(); // Get all permissions of the user depending on the attached roles

        // If the role has a global permission we don't give a shit what it does, it has access to it
        if ($this->isGlobal)
            return true;

        $parsed = $this->parseAction($action); // at this point we have namespace, controller and method of the request/action here
        if (
            isset($this->permissions[$parsed['namespace']]) ||
            isset($this->permissions[$parsed['namespace'] . '\\' . $parsed['controller']]) ||
            (
                isset($this->permissions[$parsed['namespace'] . '\\' . $parsed['controller'] . '@' . $parsed['method']]) ||
                isset($this->permissions[$parsed['namespace'] . '\\All@All']) ||
                isset($this->permissions[$parsed['namespace'] . '\\' . $parsed['controller'] . '@All'])
            )
        )
            return true;

        return false;
    }

    /**
     * Get all the permissions for a user
     */
    protected function getPermissions()
    {
        if (config('ohrana.cache.enabled'))
            $permissions = $this->fetchPermissionsFromCached();
        else
            $permissions = $this->fetchAllPermissionsFromDb();

        $this->permissions = [];
        if (count($permissions)) {
            foreach ($permissions as $permission) {
                if ($permission->isGlobal())
                    $this->isGlobal = true;

                if ($permission->getNamespace() && !$permission->getController() && !$permission->getMethod())
                    $this->permissions[$permission->getNamespace()] = $permission;
                else if ($permission->getNamespace() && $permission->getController() && !$permission->getMethod())
                    $this->permissions[$permission->getNamespace() . '\\' . $permission->getController()] = $permission;
                else if ($permission->getNamespace() && $permission->getController() && $permission->getMethod())
                    $this->permissions[$permission->getNamespace() . '\\' . $permission->getController() . '@' . $permission->getMethod()] = $permission;
            }
        }
    }

    /**
     * Gets all user's permissions from the database
     * @return array
     * @throws NoRepositoryException
     */
    protected function fetchAllPermissionsFromDb()
    {
        if (!$this->permissionRepository)
            throw new NoRepositoryException('getPermissionsFromDb');

        $permissions = [];
        foreach ($this->roles as $role) {
            $dbPermissions = $this->permissionRepository->getPermissions($role->getId());

            if (count($dbPermissions))
            {
                foreach ($dbPermissions as $permission) {
                    $parsed = $this->parsePermission($permission->getRule());

                    if ($parsed['namespace'] == 'All')
                        $permission->setGlobal(true);

                    $permission->setNamespace($parsed['namespace']);
                    $permission->setController($parsed['controller']);
                    $permission->setMethod($parsed['method']);

                    $permissions[] = $permission;
                }
            }
        }

        return $permissions;
    }

    /**
     * Gets user's permissions for a specific role from the database
     * @return array
     * @throws NoRepositoryException
     */
    protected function fetchPermissionsFromDb($roleId)
    {
        if (!$this->permissionRepository)
            throw new NoRepositoryException('getPermissionsFromDb');

        $permissions = $this->permissionRepository->getPermissions($roleId);
        if (count($permissions))
        {
            foreach ($permissions as $permission) {
                $parsed = $this->parsePermission($permission->getRule());

                if ($parsed['namespace'] == 'All')
                    $permission->setGlobal(true);

                $permission->setNamespace($parsed['namespace']);
                $permission->setController($parsed['controller']);
                $permission->setMethod($parsed['method']);
            }
        }

        return $permissions;
    }

    /**
     * Gets user's permissions from the cache
     * @return array
     */
    protected function fetchPermissionsFromCached()
    {
        foreach ($this->roles as $role) {
            $permissions = \Cache::remember(
                    config('ohrana.cache.prefix') . '_role_' . $role->getId(),
                    config('ohrana.cache.lifetime'),
                    function () use ($role) {
                        return $this->fetchPermissionsFromDb($role->getId());
                    });
        }

        return $permissions;
    }

    /**
     * Flush the cache for user's roles
     */
    protected function flushCache($roleId)
    {
        if (config('ohrana.cache.enabled'))
            \Cache::forget(config('ohrana.cache.prefix') . "_role_" . $roleId);
    }
}