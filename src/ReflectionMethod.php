<?php

declare(strict_types=1);

namespace Cwola\Reflector;

use ReflectionMethod as GlobalReflectionMethod;

class ReflectionMethod implements Reflectable {
    /**
     * @var \ReflectionMethod
     */
    protected GlobalReflectionMethod $reflection;

    /**
     * @var object?
     */
    protected ?object $object;


    /**
     * @param \ReflectionMethod $reflection
     * @param object? $object [optional]
     */
    public function __construct(GlobalReflectionMethod $reflection, ?object $object = null) {
        $this->reflection = $reflection;
        $this->object = $object;
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
     * @param bool $accessible
     *
     * @return $this
     */
    public function accessible(bool $accessible): static {
        $this->setAccessible($accessible);
        return $this;
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function call(mixed ...$args): mixed {
        return $this->invokeArgs($this->object, array_merge([$this->object], $args));
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function invoke(mixed ...$args): mixed {
        return $this->call(...$args);
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
