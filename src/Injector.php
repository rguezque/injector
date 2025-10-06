<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\Injector;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use rguezque\Exceptions\ClassNotFoundException;
use rguezque\Exceptions\DependencyNotFoundException;
use rguezque\Exceptions\DuplicityException;

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
     * @param callable|null $object Dependency
     * @return Dependency
     * @throws DuplicityException
     */
    public function add(string $name, ?callable $object = null): Dependency {
        if($this->has($name)) {
            throw new DuplicityException(sprintf('Already exists a dependency with name "%s".', $name));
        }

        $object = $object ?? $name;    
        $dependency = new Dependency($object);
        $this->dependencies[$name] = $dependency;

        return $dependency;
    }
    
    /**
     * Retrieves a dependency
     * 
     * @param string $name Dependency name
     * @return mixed
     * @throws DependencyNotFoundException
     * @throws ClassNotFoundException
     */
    public function get(string $name, array $arguments = []): mixed {
        if(!$this->has($name)) {
            throw new DependencyNotFoundException(sprintf('Don\'t exists a dependency with name "%s".', $name));
        }

        // Retrieve the dependency
        $dependency_object = $this->dependencies[$name];
        $dependency = $dependency_object->getDependency();
        $dependency_args = $dependency_object->getArguments();
        
        if($dependency instanceof Closure) {
            return $this->execDependency($dependency, $dependency_args, $arguments);
        } 
        
        if(is_array($dependency)) {
            list($class, $method) = $dependency;

            if(!class_exists($class)) {
                throw new ClassNotFoundException(sprintf('Don\'t exists the class "%s".', $class));
            }

            $rm = new ReflectionMethod($class, $method);

            if(!$rm->isStatic()) {
                $rc = new ReflectionClass($class);
                $class = $rc->newInstance();
                $dependency = [$class, $method];
            }

            return $this->execDependency($dependency, $dependency_args, $arguments);
        }

        if(!class_exists($dependency)) {
            throw new ClassNotFoundException(sprintf('Don\'t exists the class "%s".', $dependency));
        }

        $class = new ReflectionClass($dependency);

        if([] !== $dependency_args) {
            $mapped_parameters = array_map(function($arg) {
                if(is_string($arg) && $this->has($arg)) {
                    $arg = $this->get($arg);
                }
                return $arg;
            }, $dependency_args);
            
            if([] !== $arguments) {
                $mapped_parameters = array_merge($mapped_parameters, $arguments);
            }
            return $class->newInstanceArgs($mapped_parameters);
        } 
        
        return [] !== $arguments ? $class->newInstanceArgs($arguments) : $class->newInstance();
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
     * Returns the result of executing a dependency of Closure type or static method
     * 
     * @param mixed $dependency The dependency to execute
     * @param array $parameters Arguments injected to dependency
     * @param array $arguments Arguments sent when dependency is called
     * @return mixed
     */
    private function execDependency($dependency, array $parameters, array $arguments): mixed {
        return [] !== $arguments ? call_user_func_array($dependency, array_values(array_merge($parameters, $arguments))) : call_user_func($dependency, ...$parameters);
    }

}

?>