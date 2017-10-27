<?php
namespace stoykov\Ohrana;

class Controller
{
    /**
     * The controller path
     * @var string
     */
    protected $fileName;

    /**
     * The controller name with it's namespace
     * @var string
     */
    protected $namespace;

    /**
     * Controller name
     * @var string
     */
    protected $name;

    /**
     * All the public methods of the controller
     * @var array
     */
    protected $methods = [];

    /**
     * Is this data coming from the cache
     * @var bool
     */
    protected $cached = false;

    public function __construct($filename, $data=null)
    {
        $this->fileName = $filename;

        if (!$data) {
            $this->name = $this->_parseFileName();

            $this->_setNamespace();
            $this->_setMethods();
        } else {
            $this->namespace    = $data['namespace'];
            $this->name         = $data['name'];
            $this->methods      = $data['methods'];
            $this->cached       = true;
        }
    }

    /**
     * Gets the namespace of the controller
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns the name of the controller
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return all the public methods in this controller
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Returns if this controller was generated from cache
     * @return boolean
     */
    public function isCached()
    {
        return $this->cached;
    }

    /**
     * Returns an array with the object's attributes
     * @return array
     */
    public function toArray()
    {
        return [
            'fileName' => $this->fileName,
            'namespace' => $this->namespace,
            'name' => $this->name,
            'methods' => $this->methods
        ];
    }

    /**
     * Sets the namespace of the controller
     */
    private function _setNamespace()
    {
        $contents = file_get_contents($this->fileName);
        if (preg_match('#^namespace\s+(.+?);$#sm', $contents, $match)) {
            $this->namespace = $match[1];
        } else {
            $this->namespace = "";
        }
    }

    /**
     * Gets all the public methods from a given class
     * and populates the methods attribute
     */
    private function _setMethods()
    {
        $class = new \ReflectionClass("\\" . $this->namespace . "\\" . $this->name);

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class == ($this->namespace . "\\" . $this->name))
                $this->methods[] = $method->name;
        }
    }

    /**
     * Parses the name of the controller from absolute path
     * @return string
     */
    private function _parseFileName()
    {
        $splitName = explode("/", $this->fileName);
        $noExt = explode(".", end($splitName));
        unset($noExt[count($noExt) - 1]);
        return implode(".", $noExt);
    }
}