<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2021 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace Forge\Injector;

use Closure;
use LogicException;
use OutOfBoundsException;
use ReflectionClass;

/**
 * Contenedor de dependencias.
 */
class Injector implements InjectorInterface {

    /**
     * Almacena los contenedores de dependencias
     * 
     * @var Dependency[]
     */
    private $dependencies = array();

    /**
     * {@inheritdoc}
     */
    public function add(string $name, $object = null) {
        if($this->has($name)) {
            throw new LogicException(sprintf('Ya existe una dependencia registrada con el nombre (%s)', $name));
        }

        $object = $object ?? $name;    
            
        $dependency = new Dependency($object);
        $this->dependencies[$name] = $dependency;

        if(!$object instanceof Closure) {
            return $dependency;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function get(string $name) {
        if(!$this->has($name)) {
            throw new OutOfBoundsException(sprintf('No existe la dependencia solicitada con el nombre (%s)', $name));
        }

        // Recupera la dependencia
        $dependency = $this->dependencies[$name];
        
        if($dependency->getDependency() instanceof Closure) {
            $closure = $dependency->getDependency();

            return $closure();
        } else {
            $ref = new ReflectionClass($dependency->getDependency());

            // Si la dependencia tiene parámetros, se procesan
            if(!empty($dependency->getParameters())) {
                $temp = array();

                foreach ($dependency->getParameters() as $param) {
                    // Si el parámetro es string y aparece en la lista de dependencias se invoca recursivamente
                    if(is_string($param) && $this->has($param)) {
                        $ref_param = $this->get($param);
                        $temp[] = $ref_param;
                    } else { // Si el parámetro no es una dependencia simplemente se agrega al listado
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
     * {@inheritdoc}
     */
    public function has(string $name): bool {
        return array_key_exists($name, $this->dependencies);
    }

}

?>