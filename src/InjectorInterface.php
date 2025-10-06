<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2021 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\Injector;

/**
 * Contenedor de dependencias.
 */
interface InjectorInterface {

    /**
     * Add a dependency to container
     * 
     * @param string $name Dependendy name
     * @param callable|null $object Dependency
     * @return Dependency
     * @throws DuplicityException
     */
    public function add(string $name, $class = null): Dependency;

    /**
     * Retrieves a dependency
     * 
     * @param string $name Dependency name
     * @return mixed
     * @throws DependencyNotFoundException
     * @throws ClassNotFoundException
     */
    public function get(string $name): mixed;

    /**
     * Returns true if a dependency exists
     * 
     * @param string $name Dependency name
     * @return bool
     */
    public function has(string $name): bool;

}

?>