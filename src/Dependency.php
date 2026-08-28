<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2021 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\Injector;

use Closure;

/**
 * Represents a dependency and its arguments
 */
class Dependency {

    /** Dependency definition */
    private string|array|Closure|null $dependency = null;

    /** Dependency name */
    private ?string $name = null;

    /** Arguments for dependency */
    private array $arguments = array();

    /** Define a dependency as Singleton */
    private bool $shared = false;

    /** Represents the resolved instance from the dependency */
    private mixed $resolved_instance = null;

    /**
     * Dependency definition
     * 
     * @param string|array|Closure Dependency to store
     */
    public function __construct(string|array|Closure $dependency) {
        $this->dependency = $dependency;
    }

    /**
     * Add an argument
     * 
     * @param mixed $argument Argument to inject
     * @return Dependency
     */
    public function addArgument(mixed $argument): Dependency {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * Add one or more arguments
     * 
     * @param array $arguments Arguments to inject
     * @return Dependency
     */
    public function addArguments(array $arguments): Dependency {
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }

    /**
     * Retrieve the dependency
     * 
     * @return string|array|Closure Dependency stored
     */
    public function getDependency(): string|array|Closure|null {
        return $this->dependency;
    }

    /**
     * Retrieve the dependency arguments
     * 
     * @return array
     */
    public function getArguments(): array {
        return array_values($this->arguments);
    }

    /**
     * Set dependency name
     * 
     * @param string $name Dependency name
     * @return Dependency
     */
    public function setName(string $name): Dependency {
        $this->name = trim($name);
        return $this;
    }

    /**
     * Retrieve dependency name
     * 
     * @return string Dependency name
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set the dependency as Singleton behaviour
     * 
     * @param bool $shared By default is `true`
     * @return Dependency
     */
    public function setShared(bool $shared = true): Dependency {
        $this->shared = $shared;
        return $this;
    }

    /**
     * Returns `true` if dependency is set as Singleton, otherwise `false`
     * 
     * @return bool
     */
    public function isShared(): bool {
        return $this->shared;
    }

    /**
     * Set the resolved dependency
     * 
     * @param mixed $instance The dependency already resolved
     * @return void
     */
    public function setResolvedInstance(mixed $instance): void {
        $this->resolved_instance = $instance;
    }

    /**
     * Returns the resolved dependency
     * 
     * @return mixed
     */
    public function getResolvedInstance(): mixed {
        return $this->resolved_instance;
    }

    /**
     * Returns `true` if dependency is already resolved, otherwise `false`
     * 
     * @return bool
     */
    public function hasResolvedInstance(): bool {
        return $this->resolved_instance !== null;
    }
}

?>