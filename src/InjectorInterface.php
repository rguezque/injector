<?php
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2020 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace Forge\Injector;

interface InjectorInterface {

    public function add(string $name, $class = null);
    public function get(string $name);
    public function has(string $name);
    public function exists(string $name);

}

?>