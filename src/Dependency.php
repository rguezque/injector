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

    /**
     * Dependency definition
     * 
     * @param string|Closure Dependency to store
     */
    public function __construct($dependency) {
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
        $this->arguments = array_merge($arguments, $this->arguments);

        return $this;
    }

    /**
     * Retrieve the dependency
     * 
     * @return string|array|Closure Dependency stored
     */
    public function getDependency() {
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
     */
    public function setName(string $name): void {
        $this->name = trim($name);
    }

    /**
     * Retrieve dependency name
     * 
     * @return string Dependency name
     */
    public function getName(): string {
        return $this->name;
    }

}

?>