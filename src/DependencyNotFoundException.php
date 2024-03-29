<?php declare(strict_types = 1);

namespace Forge\Injector;
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2021 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

use Exception;

/**
 * Lanza una excepción cuando no se encuentra una dependencia en el contenedor.
 */
class DependencyNotFoundException extends Exception {

}

?>