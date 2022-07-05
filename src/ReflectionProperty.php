<?php

declare(strict_types=1);

namespace Cwola\Reflector;

use ReflectionProperty as GlobalReflectionProperty;
use ReflectionException;

class ReflectionProperty implements Reflectable {
    /**
     * @var \ReflectionProperty
     */
    protected GlobalReflectionProperty $reflection;

    /**
     * @var object?
     */
    protected ?object $object;


    /**
     * @param \ReflectionProperty $reflection
     * @param object? $object [optional]
     */
    public function __construct(GlobalReflectionProperty $reflection, ?object $object = null) {
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
     * @param mixed $value
     *
     * @return $this
     *
     * @throws \ReflectionException
     */
    public function set(mixed $value): static {
        if ($this->isStatic()) {
            $this->setValue($value);
        } else if (is_object($this->object)) {
            $this->setValue($this->object, $value);
        } else {
            throw new ReflectionException();
        }
        return $this;
    }

    /**
     * @param void
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function get(): mixed {
        if ($this->isStatic()) {
            return $this->getValue();
        } else if (is_object($this->object)) {
            return $this->getValue($this->object);
        }
        throw new ReflectionException();
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
