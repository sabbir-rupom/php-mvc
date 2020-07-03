<?php

namespace Core;

use Core\Log\Logger;

/**
 * Error and exception handler class
 */
class Error
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int       $level      Error level
     * @param string    $message    Error message
     * @param string    $file       Filename the error was raised in
     * @param int       $line       Line number in the file
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param   Exception $exception  The exception
     *
     * @return  void
     */
    public static function exceptionHandler($exception)
    {
        // Code 404 (not found)
        // Code 500 (server error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }

        http_response_code($code);

        $htmlData = '';

        if ($code === 404) {
            $htmlData = "<h1 class=\"text-center\">$code</h1><h3 class=\"text-center\">Page not found</h3>";
        }

        if (SHOW_ERROR) {
            // If SHOW_ERROR is enabled from environment file
            // view error messages in custom view page
            if ($code !== 404) {
                $htmlData = "<h1>Fatal error</h1>";
                $htmlData .= "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
                $htmlData .= "<p>Message: '" . $exception->getMessage() . "'</p>";
                $htmlData .= "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
                $htmlData .= "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
            }
        } else {
            //$log = ROOT_PATH . '/logs/error-' . date('Y-m-d') . '.log';
            //ini_set('error_log', $log);
            //error_log('');

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            $logger = new Logger();
            $logger->create($message);

            $htmlData = "<h1 class=\"text-center\">$code</h1><h3 class=\"text-center\">Server error occured</h3>";
        }

        $data = [
          'code' => $code,
          'html' => $htmlData,
          'viewFile' => 'error'
        ];
        view('template/main', $data);
    }
}
