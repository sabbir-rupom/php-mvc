<?php

namespace Core;

use \Exception;

/**
 * View class
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view from $variable (optional)
     *
     */
    public static function render(string $view, array $args = [])
    {
        if (!empty($args)) {
            extract($args, EXTR_SKIP);
        }

        if (strpos($view, '.php') === false && strpos($view, '.html') === false) {
            $view .= '.php';
        }

        $file = VIEW_PATH . DIRECTORY_SEPARATOR . $view;

        // Start Output Buffering
        ob_start();

        if (is_readable($file)) {
            include($file);
        } else {
            throw new Exception("{$file} not found in view");
        }

        // End Output Buffering
        ob_end_flush();
    }
}
