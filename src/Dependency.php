<?php
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2021 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace Forge\Injector;

/**
 * Representa una dependencia y sus parámetros.
 */
class Dependency {

    /**
     * Definición de la dependencia guardada
     * 
     * @var string|Closure
     */
    private $dependency;

    /**
     * Parámetros enviados a la dependencia
     * 
     * @var array
     */
    private $arguments = array();

    /**
     * Constructor
     * 
     * @param string|Closure Recibe la definición de la dependencia
     */
    public function __construct($dependency) {
        $this->dependency = $dependency;
    }

    /**
     * Agrega un parámetro a ser inyectado
     * 
     * @param mixed $parameter Parámetro a inyectar
     * @return Container
     */
    public function addParameter($parameter): Container {
        $this->arguments[] = $parameter;

        return $this;
    }

    /**
     * Agrega uno o varios parámetros a ser inyectados
     * 
     * @param array $parameters Parámetros a inyectar
     * @return Container
     */
    public function addParameters(array $parameters): Container {
        $this->arguments = array_merge($parameters, $this->arguments);

        return $this;
    }

    /**
     * Devuelve la definición de la dependencia
     * 
     * @return string|Closure La dependencia almacenada
     */
    public function getDependency() {
        return $this->dependency;
    }

    /**
     * Devuelve los parámetros a ser inyectados a la dependencia
     * 
     * @return array
     */
    public function getParameters() {
        return $this->arguments;
    }

}

?>