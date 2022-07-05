<?php

declare(strict_types=1);

namespace Cwola\Reflector;

use ReflectionClass as GlobalReflectionClass;
use ReflectionException;

class ReflectionClass implements Reflectable {

    /**
     * @var \ReflectionClass
     */
    protected GlobalReflectionClass $reflection;

    /**
     * @var object?
     */
    protected ?object $object;

    /**
     * @var array
     */
    protected array $cache;

    /**
     * @var bool
     */
    protected bool $useCache;


    /**
     * @param object|string $objectOrClass
     * @param bool $useCache [optional]
     *
     * @return \Cwola\Reflector\ReflectionClass
     */
    public static function make(object|string $objectOrClass, bool $useCache = true): static {
        if ($objectOrClass instanceof GlobalReflectionClass) {
            return new static($objectOrClass, null, $useCache);
        } else if (is_string($objectOrClass)) {
            return new static(new GlobalReflectionClass($objectOrClass), null, $useCache);
        }
        return new static(new GlobalReflectionClass($objectOrClass), $objectOrClass, $useCache);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param object? $object [optional]
     * @param bool $useCache [optional]
     */
    public function __construct(GlobalReflectionClass $reflection, ?object $object = null, bool $useCache = true) {
        $this->reflection = $reflection;
        $this->object = $object;
        $this->cache = [];
        $this->useCache($useCache);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed {
        return $this->reflection->$name(...$arguments);
    }

    /**
     * @param string $method
     *
     * @return \Cwola\Reflector\ReflectionMethod
     *
     * @throws \ReflectionException
     */
    public function method(string $method): ReflectionMethod {
        $reflection = $this->reflection;
        $id = 'method_' . $method;
        if ($this->useCache && isset($this->cache[$id])) {
            return $this->cache[$id];
        }

        while ($reflection instanceof GlobalReflectionClass) {
            if ($reflection->hasMethod($method)) {
                $reflectionMethod = new ReflectionMethod($reflection->getMethod($method), $this->object);
                if ($this->useCache) {
                    $this->cache[$id] = $reflectionMethod;
                }
                return $reflectionMethod;
            }
            $reflection = $this->getParentClass();
        }
        throw new ReflectionException(sprintf('Method %s does not exist.', $method));
    }

    /**
     * @param string $property
     *
     * @return \Cwola\Reflector\ReflectionProperty
     *
     * @throws \ReflectionException
     */
    public function property(string $property): ReflectionProperty {
        $reflection = $this->reflection;
        $id = 'property_' . $property;
        if ($this->useCache && isset($this->cache[$id])) {
            return $this->cache[$id];
        }

        while ($reflection instanceof GlobalReflectionClass) {
            if ($reflection->hasProperty($property)) {
                $reflectionProperty = new ReflectionProperty($reflection->getProperty($property), $this->object);
                if ($this->useCache) {
                    $this->cache[$id] = $reflectionProperty;
                }
                return $reflectionProperty;
            }
            $reflection = $this->getParentClass();
        }
        throw new ReflectionException(sprintf('Property %s does not exist.', $property));
    }

    /**
     * @param bool $useCache
     *
     * @return $this
     */
    public function useCache(bool $useCache): static {
        if ($this->useCache !== $useCache) {
            $this->cache = [];
        }
        $this->useCache = $useCache;
        return $this;
    }

    /**
     * @param void
     *
     * @return object|null
     */
    public function getObject(): object|null {
        return $this->object;
    }
}
