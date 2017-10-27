<?php
namespace stoykov\Ohrana\Models\Doctrine;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ohrana_permissions")
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="role_id")
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $roleId;

    /**
     * @ORM\Column(type="string")
     */
    private $rule;

    /**
     * Namespace parsed from action
     * (parsed only when fetched from db and saved to cache)
     * @var string
     */
    private $namespace;

    /**
     * Controller parsed from action
     * (same as namespace)
     * @var string
     */
    private $controller;

    /**
     * Method parsed from action
     * (same as namespace)
     * @var string
     */
    private $method;

    /**
     * Is that a global access permission?
     * @var bool
     */
    private $isGlobal = false;

    /**
    * @param int    $id
    * @param int    $roleId
    * @param string $action
    */
    public function __construct($id, $roleId, $action, $parsed = null)
    {
        $this->id = $id;
        $this->roleId = $roleId;
        $this->action = $action;

        if ($parsed) {
            $this->namespace    = $parsed['namespace'];
            $this->controller   = $parsed['controller'];
            $this->method       = $parsed['method'];
        }
    }

    /**
     * Gets the id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the role id
     * @return int
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Sets the role id
     * @param int $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Returns the rule
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Sets the rule property
     * @param string $rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return bool
     */
    public function isGlobal()
    {
        return $this->isGlobal;
    }

    /**
     * @param bool $isGlobal
     */
    public function setGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'            => $this->id,
            'role_id'       => $this->roleId,
            'action'        => $this->action,
            'namespace'     => $this->namespace,
            'controller'    => $this->controller,
            'method'        => $this->method,
            'is_global'     => $this->isGlobal,
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'The Permission model cannot be converted to string.';
    }
}