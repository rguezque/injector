<?php
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2020 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace Forge\Injector;

class Container {

    /**
     * Definición de la dependencia guardada
     * 
     * @var string|closure
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
     * @param string Recibe la definición de la dependencia
     */
    public function __construct($dependency) {
        $this->dependency = $dependency;
    }

    /**
     * Agrega un parámetro a ser inyectado
     * 
     * @param mixed $parameter Parámetro a inyectar
     */
    public function addParameter($parameter) {
        $this->arguments[] = $parameter;

        return $this;
    }

    /**
     * Agrega uno o varios parámetros a ser inyectados
     * 
     * @param array $parameters Parámetros a inyectar
     */
    public function addParameters(array $parameters) {
        $this->arguments = array_merge($parameters, $this->arguments);
    }

    /**
     * Devuelve la definición de la dependencia
     * 
     * @param void
     * @return string|closure La dependencia almacenada
     */
    public function getDependency() {
        return $this->dependency;
    }

    /**
     * Devuelve los parámetros a ser inyectados a la dependencia
     * 
     * @param void
     * @return array
     */
    public function getParameters() {
        return $this->arguments;
    }

}

?>