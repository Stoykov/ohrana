<?php
namespace stoykov\Ohrana;

use Illuminate\Container\Container;

class ACLContainer {

    /**
     * Application instance.
     *
     * @var Illuminate\Contracts\Foundation\Application|Laravel\Lumen\Application
     */
    protected $app;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * All the available controllers
     *
     * @var array
     */
    protected $controllers = [];

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Get all additional paths.
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get scanned modules paths.
     *
     * @return array
     */
    public function getScanPaths() : array
    {
        $paths = $this->paths;

        if ($this->config('scan.enabled')) {
            $paths = array_merge($paths, $this->config('scan.paths'));
        }

        return $paths;
    }

    /**
     * Get all controllers.
     *
     * @return array
     */
    public function all() : array
    {
        if (!$this->config('cache.enabled')) {
            return $this->scan();
        }

        return $this->formatCached($this->getCached());
    }

    /**
     * Get cached controllers.
     *
     * @return array
     */
    public function getCached()
    {
        return $this->app['cache']->remember($this->config('cache.key'), $this->config('cache.lifetime'), function () {
            return $this->toCollection()->toArray();
        });
    }

    /**
     * Clear cached controllers.
     *
     * @return array
     */
    public function clearCached()
    {
        return $this->app['cache']->forget($this->config('cache.key'));
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles()
    {
        return $this->app['files'];
    }

    /**
     * Scan to get all controllers
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $allControllers = [];

        foreach ($paths as $key => $path) {
            if (strstr($path, "Controllers")) {
                $controllers = $this->app['files']->glob("{$path}*.php");
                $controllers = array_merge($this->app['files']->glob("{$path}/*.php"), $controllers);
            } else {
                $controllers = $this->app['files']->glob("{$path}/Http/Controllers/*.php");
            }

            is_array($controllers) || $controllers = [];

            foreach ($controllers as $controller) {
                $cont = new Controller($controller);
                $allControllers[$cont->getNamespace().$cont->getName()] = $cont;
            }
        }

        return $allControllers;
    }

    /**
     * Format the cached data as array of controllers.
     *
     * @param array $cached
     *
     * @return array
     */
    protected function formatCached($cached)
    {
        $allControllers = [];

        foreach ($cached as $name => $controller) {
            $allControllers[$controller['namespace'].$controller['name']] = new Controller($controller['fileName'], $controller);
        }

        return $allControllers;
    }

    /**
     * Get a specific config data from a configuration file.
     *
     * @param $key
     *
     * @param null $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return $this->app['config']->get('ohrana.' . $key, $default);
    }

    /**
     * Get all controllers as collection instance.
     *
     * @return Collection
     */
    public function toCollection() : Collection
    {
        return new Collection($this->scan());
    }
}