<?php
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2020 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace Forge\Injector;

use Forge\Injector\Container;
use Forge\Injector\InjectorInterface;

class Injector implements InjectorInterface {

    /**
     * Almacena los contenedores de dependencias
     * 
     * @var Container[]
     */
    private $dependencies = array();

    /**
     * Agrega una dependencia al contenedor
     * 
     * @param string $name Nombre o alias de la dependencia
     * @param string|closure $object Dependencia a guardar
     * @return object
     */
    public function add(string $name, $object = null) {
        if($this->has($name))
            throw new \Exception(sprintf('Ya existe una dependencia registrada con el nombre (%s)', $name));

        $object = $object ?? $name;    
            
        $container = new Container($object);
        $this->dependencies[strtolower($name)] = $container;

        if(!is_object($object) && !is_callable($object))
            return $container;
    }
    
    /**
     * Recupera una dependencia del contenedor
     * 
     * @param string $name Nombre o alias de la dependencia
     * @return object|closure
     * @throws \Exception
     */
    public function get(string $name) {
        if(!$this->has($name) && !$this->exists($name))
            throw new \Exception(sprintf('No existe la dependencia solicitada con el nombre (%s)', $name));

        // Recupera la dependencia
        $container = $this->dependencies[strtolower($name)];
        
        if(is_object($container->getDependency()) || is_callable($container->getDependency())) {
            $closure = $container->getDependency();

            return $closure();
        } else {
            $ref = new \ReflectionClass($container->getDependency());

            if(!empty($container->getParameters())) {
                $temp = array();

                foreach ($container->getParameters() as $param) {
                    if(is_string($param) && $this->exists($param)) {
                        $ref_param = $this->get($param);
                        $temp[] = $ref_param;
                    } else {
                        $temp[] = $param;
                    }
                }

                return $ref->newInstanceArgs($temp);
            } else {
                
                return $ref->newInstance();
            }
        }
    }
    
    /**
     * Verifica si una dependencia esta registrada según su nombre o alias
     * 
     * @param string $name Nombre o alias de la dependencia
     * @return bool
     */
    public function has(string $name) {
        return array_key_exists($name, $this->dependencies);
    }

    /**
     * Verifica si una dependencia existe realmente según su nombre o alias
     * 
     * @param string $name Nombre o alias de la dependencia
     * @param bool
     */
    public function exists(string $name) {
        return class_exists($name);
    }

}

?>