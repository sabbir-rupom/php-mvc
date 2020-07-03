<?php

namespace Core;

use \Exception;

/**
 * Base controller
 */
abstract class Controller
{
    
    /**
     * Magic method __call function is used to handle non-existent methods if called from an App Controller
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name;

        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            throw new Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Render App main page with user view file
     *
     * @param string $viewFile View file path
     * @param type $args Arguments to be passed into view files
     */
    protected function renderView(string $viewFile, array $args = [])
    {
        $data = array_merge($args, [
          'viewFile' => $viewFile
        ]);

        view('template/main', $data);
    }
}
