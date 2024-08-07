<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\Injector;

/**
 * Facade for Injector (Dependencies container)
 */
class Container {

    /**
     * Store the dependency container
     * 
     * @var Injector
     */
    private static Injector $injector;

    /**
     * Allow call statically the methods of Injector
     * 
     * @param string $name Method name
     * @param array 4params Method parameters
     * @return mixed
     */
    public static function __callStatic(string $name, array $params) {
        $app = self::app();

        return call_user_func_array([$app, $name], array_values($params));
    }

    /**
     * Return a Injector object. Implement Singleton pattern
     * 
     * @return Injector
     */
    public static function app(): Injector {
        static $initialized = false;

        if (!$initialized) {
            self::$injector = new Injector();

            $initialized = true;
        }

        return self::$injector;
    }
}

?>