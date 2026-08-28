<?php

declare(strict_types=1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2026 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\Injector;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use rguezque\Exceptions\ClassNotFoundException;
use rguezque\Exceptions\DependencyNotFoundException;
use rguezque\Exceptions\DuplicityException;
use rguezque\Exceptions\ResolutionException;

/**
 * Dependencies container.
 * 
 * @method void|Dependency add(string $name, callable $object = null) Add a dependency to container
 * @method object|Closure get(string $name) Retrieves a dependency
 * @method bool has(string $name) Returns true if a dependency exists
 */
class Injector {

    /**
     * Dependencies collection
     * 
     * @var array<string, Dependency>
     */
    private $dependencies = [];

    /**
     * Add a dependency to container
     * 
     * @param string $name Dependendy name
     * @param callable|string|null $object Dependency
     * @return Dependency
     * @throws DuplicityException
     */
    public function add(string $name, callable|string|null $object = null): Dependency {
        if ($this->has($name)) {
            throw new DuplicityException(sprintf('Already exists a dependency with name "%s".', $name));
        }

        $object = $object ?? $name;
        $dependency = new Dependency($object);
        $dependency->setName($name);

        $this->dependencies[$name] = $dependency;
        return $dependency;
    }

    /**
     * Retrieves a dependency
     * 
     * @param string $name Dependency name
     * @param array $explicit_arguments Additional arguments to send
     * @return mixed
     * @throws DependencyNotFoundException
     * @throws ClassNotFoundException
     */
    public function get(string $name, array $explicit_arguments = []): mixed {
        if (!$this->has($name)) {
            throw new DependencyNotFoundException(sprintf('Dependency "%s" not found.', $name));
        }

        $dependency = $this->dependencies[$name];

        // Support for Singleton / Shared instances
        if ($dependency->isShared() && $dependency->hasResolvedInstance()) {
            return $dependency->getResolvedInstance();
        }

        $dep_value = $dependency->getDependency();
        $dep_args = $dependency->getArguments();

        $resolved_instance = null;

        // Resolution for Closures
        if ($dep_value instanceof Closure) {
            $reflector = new ReflectionFunction($dep_value);
            $args = $this->resolveParameters($reflector, $dep_args, $explicit_arguments);
            $resolved_instance = $dep_value(...$args);
        }
        // Resolution for Arrays [Class, Method]
        elseif (is_array($dep_value)) {
            [$class_name, $method] = $dep_value;
            if (!class_exists($class_name)) {
                throw new ClassNotFoundException(sprintf('Class "%s" does not exist.', $class_name));
            }

            $reflector = new ReflectionMethod($class_name, $method);

            if (!$reflector->isStatic()) {
                // Resolve correctly the instance class (with autowiring)
                $class_reflector = new ReflectionClass($class_name);
                $instance_args = $this->resolveParameters($class_reflector->getConstructor(), [], []);
                $instance = $class_reflector->newInstanceArgs($instance_args);
                $resolved_instance = $instance->$method(...$this->resolveParameters($reflector, $dep_args, $explicit_arguments));
            } else {
                $resolved_instance = $class_name::$method(...$this->resolveParameters($reflector, $dep_args, $explicit_arguments));
            }
        }
        // Resolution for Class Names (String)
        else {
            if (!class_exists($dep_value)) {
                throw new ClassNotFoundException(sprintf('Class "%s" does not exist.', $dep_value));
            }
            $reflector = new ReflectionClass($dep_value);
            $constructor = $reflector->getConstructor();

            $args = $this->resolveParameters($constructor, $dep_args, $explicit_arguments);
            $resolved_instance = $reflector->newInstanceArgs($args);
        }

        // Save shared instance (Singleton)
        if ($dependency->isShared()) {
            $dependency->setResolvedInstance($resolved_instance);
        }

        return $resolved_instance;
    }

    /**
     * Returns true if a dependency exists
     * 
     * @param string $name Dependency name
     * @return bool
     */
    public function has(string $name): bool {
        return array_key_exists($name, $this->dependencies);
    }

    /**
     * Resolve the parameters of a constructor (`__construct`), closure, or method using Autowiring
     * 
     * @param ?object $reflector The `Reflection` object to resolve
     * @param array $dep_args Arguments from dependency
     * @param array $explicit_args Optional arguments to send to `Injector::get`
     * @return array
     */
    private function resolveParameters(?object $reflector, array $dep_args, array $explicit_args): array {
        if ($reflector === null) {
            return array_merge($dep_args, $explicit_args);
        }

        $parameters = $reflector->getParameters();
        $resolved = [];

        foreach ($parameters as $index => $parameter) {
            $paramName = $parameter->getName();

            // Explicit arguments send to `Injector::get` (by name or index)
            if (array_key_exists($paramName, $explicit_args)) {
                $resolved[] = $explicit_args[$paramName];
                continue;
            }
            if (array_key_exists($index, $explicit_args)) {
                $resolved[] = $explicit_args[$index];
                continue;
            }

            // Arguments from Dependency
            if (array_key_exists($index, $dep_args)) {
                $resolved[] = $dep_args[$index];
                continue;
            }

            // Autowiring: If it has a class type, we try to resolve it from the container.
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $class_name = $type->getName();
                if ($this->has($class_name)) {
                    $resolved[] = $this->get($class_name);
                    continue;
                }

                // Si no está registrado pero existe la clase, intentamos instanciarla (Autowiring profundo)
                if (class_exists($class_name)) {
                    $resolved[] = $this->buildAnonymous($class_name);
                    continue;
                }
            }

            // Args with default values
            if ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
                continue;
            }

            // If the parameter can't be solved
            throw new ResolutionException(sprintf(
                'Cannot resolve parameter "$%s" in %s. No type hint, default value, or explicit argument provided.',
                $paramName,
                $reflector->getName()
            ));
        }

        return $resolved;
    }

    /**
     * Build an anonymous class (not registered in the container) with autowiring.
     * 
     * @param string $class_name The class name
     * @return mixed
     */
    private function buildAnonymous(string $class_name): mixed {
        $reflector = new ReflectionClass($class_name);
        $constructor = $reflector->getConstructor();
        $args = $this->resolveParameters($constructor, [], []);
        return $reflector->newInstanceArgs($args);
    }
}
