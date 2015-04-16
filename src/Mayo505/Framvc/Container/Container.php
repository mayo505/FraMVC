<?php  namespace Mayo505\Framvc\Container;

use Mayo505\Framvc\Container\Exceptions\ContainerException;

class Container implements ContainerInterface {

    protected $bindings = [];

    protected $instances = [];

    public function bind($toBind, $actual) {
        $this->bindings[$toBind] = $actual;
    }

    public function instance($toBind, $instance) {
        $this->instances[$toBind] = $instance;
    }

    public function make($toMake) {
        if (isset($this->instances[$toMake])) {
            return $this->instances[$toMake];
        }

        if (isset($this->shared[$toMake])) {
            return $this->returnShared($toMake);
        }

        if (isset($this->bindings[$toMake])) {
            $toCreate = $this->bindings[$toMake];
            if ($toCreate instanceof \Closure) {
                return $toCreate($this);
            }

            return $this->buildClass($toCreate);
        }

        return $this->buildClass($toMake);
    }

    public function shared($toShare, $actual) {
        if ($actual instanceof \Closure) {
            $actual = $actual($this);
        }
        $this->shared[$toShare] = $actual;
    }

    public function buildClass($className) {
        $class = new \ReflectionClass($className);

        if (! $class->isInstantiable()) {
            if (! isset($this->bindings[$className])) {
                throw new ContainerException("Given type [$className] cannot be created");
            }

            return $this->buildClass($this->bindings[$className]);
        }

        $constructor = $class->getConstructor();

        if ($constructor === null) {
            return new $className;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        return $class->newInstanceArgs($dependencies);
    }

    protected function returnShared($shared) {
        if (is_object($this->shared[$shared])) {
            return $this->shared[$shared];
        }

        $this->shared[$shared] = $this->buildClass($this->shared[$shared]);
        return $this->shared[$shared];
    }

    protected function getDependencies($parameters) {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $class = $parameter->getClass();

            if ($class === null) {
                $dependencies[] = $this->getSimpleType($parameter);
            }
            else {
                $dependencies[] = $this->make($class->name);
            }
        }

        return $dependencies;
    }

    protected function getSimpleType(\ReflectionParameter $parameter) {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException("Cannot assing value for parameter " . $parameter->getName());
    }

    public function boot() {
        $this->instance("container", $this);
        $this->instance("Mayo505\Framvc\Container\ContainerInterface", $this);
    }

}