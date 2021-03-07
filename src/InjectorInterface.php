<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2021 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace Forge\Injector;

/**
 * Contenedor de dependencias.
 */
interface InjectorInterface {

    /**
     * Agrega una dependencia al contenedor
     * 
     * @param string $name Nombre o alias de la dependencia
     * @param string|closure $object Dependencia a guardar
     * @return Dependency|void
     */
    public function add(string $name, $class = null);

    /**
     * Recupera una dependencia del contenedor
     * 
     * @param string $name Nombre o alias de la dependencia
     * @return object|Closure
     * @throws Exception
     */
    public function get(string $name);

    /**
     * Verifica si una dependencia esta registrada según su nombre o alias
     * 
     * @param string $name Nombre o alias de la dependencia
     * @return bool
     */
    public function has(string $name): bool;

}

?>