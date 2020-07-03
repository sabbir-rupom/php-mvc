<?php

if (!function_exists('env')) {

    /**
     * Parse .env file and prepare environment constants
     */
    function parseEnv(string $filePath)
    {
        try {
            $envFile = new SplFileObject($filePath);
            while ($envFile->valid()) {
                $line = trim($envFile->fgets());
                if (strpos($line, "#") !== false) {
                    $line = trim(substr($line, 0, strpos($line, "#")));
                }
                if (empty($line)) {
                    continue;
                } else {
                    $env = explode('=', $line);
                    if (isset($env[1])) {
                        $envName = strtoupper(trim($env[0]));
                        $envValue = trim($env[1]);
                        defined($envName) || define($envName, $envValue);
                    }
                }
            }
        } catch (ErrorException $exception) {
            throw new \Exception($exception->getMessage(), 500);
        }
    }
}

if (!function_exists('debug')) {

    /**
     * Debug print function with/without exit
     *
     * @param mixed $data
     * @param bool $print
     * @param bool $exit
     */
    function debug($data, bool $print = false, bool $exit = true)
    {
        if ($print) {
            echo $data;
        } else {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        if ($exit) {
            exit();
        }
    }
}

if (!function_exists('getVar')) {

    /**
     * Gets a variable from $_SERVER using $default if not provided.
     *
     * @param string $var Variable name
     * @param string $default Default value to substitute
     * @return string Server variable value
     */
    function getVar($var, $default = '')
    {
        return $_SERVER[$var] ?? $default;
    }
}

if (!function_exists('getIpAddress')) {

    /**
     * Get client IP address
     *
     * @return string
     */
    function getIpAddress(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
    }
}

if (!function_exists('getMethod')) {

    /**
     * Gets the request method.
     *
     * @return string
     */
    function getMethod(): string
    {
        $method = getVar('REQUEST_METHOD', 'GET');

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        return strtoupper($method);
    }
}


if (!function_exists('baseUrl')) {

    /**
     * Get base URL
     *
     * @return string
     */
    function baseUrl($path = ''): string
    {
        $path = ltrim($path, '/');
        return defined('BASE_URL') ? BASE_URL . $path : getProtocol() . '://' . getVar('HTTP_HOST') . '/' . $path;
    }
}

if (!function_exists('redirect')) {

    /**
     * Redirect page
     *
     * @return string
     */
    function redirect(string $routePath = '')
    {
        header('Location:' . baseUrl() . $routePath);
    }
}

if (!function_exists('getBody')) {

    /**
     * Get the body of the request
     *
     * @return string Raw HTTP request body
     */
    function getBody()
    {
        $body = null;

        $method = getMethod();

        if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE' || $method == 'PATCH') {
            $body = file_get_contents('php://input');
        }

        return $body;
    }
}

if (!function_exists('inputPost')) {

    /**
     * Get Post data with filter sanitize
     *
     * @param string $key Post parameter key
     * @param null | int $flag Filter sanitizer flag number
     * @return mixed
     */
    function inputPost(string $key = '', $flag = null)
    {
        $data = null;

        if (empty($key)) {
            if (!empty($_POST)) {
                foreach ($_POST as $k => $v) {
                    $data[$k] = in_array($flag, saniTizeFilters()) ? filter_var($v, $flag) : $v;
                }
            }
            return $data;
        }

        if (empty($_POST[$key])) {
            return null;
        }

        return in_array($flag, saniTizeFilters()) ? filter_var($_POST[$key], $flag) : $_POST[$key];
    }
}

if (!function_exists('inputGet')) {

    /**
     * Get Query String/GET data with filter sanitize
     *
     * @param string $key Query String/GET parameter key
     * @param null | int $flag Filter sanitizer flag number
     * @return mixed
     */
    function inputGet(string $key = '', $flag = null)
    {
        $data = null;

        if (empty($key)) {
            if (!empty($_GET)) {
                foreach ($_GET as $k => $v) {
                    $data[$k] = in_array($flag, saniTizeFilters()) ? filter_var($v, $flag) : $v;
                }
            }
            return $data;
        }

        if (empty($_GET[$key])) {
            return null;
        }

        return in_array($flag, saniTizeFilters()) ? filter_var($_GET[$key], $flag) : $_GET[$key];
    }
}

if (!function_exists('saniTizeFilters')) {

    /**
     * Get array of all valid sanitize filters
     *
     * @return array
     */
    function saniTizeFilters()
    {
        return [
          FILTER_SANITIZE_EMAIL,
          FILTER_SANITIZE_ENCODED,
          FILTER_SANITIZE_MAGIC_QUOTES,
          FILTER_SANITIZE_NUMBER_FLOAT,
          FILTER_SANITIZE_NUMBER_INT,
          FILTER_SANITIZE_SPECIAL_CHARS,
          FILTER_SANITIZE_FULL_SPECIAL_CHARS,
          FILTER_SANITIZE_STRING,
          FILTER_SANITIZE_STRIPPED,
          FILTER_SANITIZE_URL,
          FILTER_UNSAFE_RAW
        ];
    }
}


if (!function_exists('getProtocol')) {

    /**
     * Get HTTP protocol
     *
     * @return string http or https
     */
    function getProtocol(string $url): string
    {
        if (
            (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on') ||
            (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https')
        ) {
            return 'https';
        }
        return 'http';
    }
}

if (!function_exists('requestUri')) {

    /**
     * Get requested URL
     *
     * @return string Request URL
     */
    function requestUri(): string
    {
        return !empty(getVar('REDIRECT_URL')) ? getVar('REDIRECT_URL') : strtok(getVar('REQUEST_URI', '/'), '?');
    }
}

if (!function_exists('getDbConnection')) {

    /**
     * Get requested URL
     *
     * @return string Request URL
     */
    function getDbConnection(): PDO
    {
        $dns = 'mysql:dbname=' . DB_NAME . ";host=" . DB_HOST . ';charset=utf8';
        $db = null;
        try {
            $db = new PDO($dns, DB_USER, DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        return $db;
    }
}

if (!function_exists('view')) {

    /**
     * Function to call View class to render html page
     *
     * @param string $view
     * @param array $args
     */
    function view(string $view, array $args = [])
    {
        \Core\View::render($view, $args);
    }
}

if (!function_exists('setFlashData')) {

    /**
     * Save flash data in session
     *
     * @param mixed $data Flash data key / Array data to be saved
     * @param type $value Flash data value [ optional ]
     * @return boolean On success returns TRUE, FALSE otherwise
     */
    function setFlashData($data = null, $value = '')
    {
        if (empty($data)) {
            return false;
        }
        $flashData = isset($_SESSION['flash_data']) ? $_SESSION['flash_data'] : [];
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $flashData[$k] = $v;
            }
        } elseif (is_string($data) && !empty($value)) {
            $flashData[$data] = $value;
        }
        $_SESSION['flash_data'] = $flashData;
        return true;
    }
}

if (!function_exists('getFlashData')) {

    /**
     * Get flash data from session
     *
     * @param string $data Flash data key
     * @return mixed Return actual data or empty string
     */
    function getFlashData($data = '')
    {
        $flashData = isset($_SESSION['flash_data']) ? $_SESSION['flash_data'] : [];
        if (empty($data) && !empty($flashData)) {
            unset($_SESSION['flash_data']);
            return $flashData;
        } elseif (!isset($flashData[$data]) || empty($flashData[$data])) {
            return '';
        } else {
            unset($_SESSION['flash_data'][$data]);
            return $flashData[$data];
        }
    }
}

if (!function_exists('textMessage')) {

    /**
     * Convert a data into text message-string
     *
     * @param mixed $data
     * @return string
     */
    function textMessage($data = null): string
    {
        $msg = '';
        if (is_array($data)) {
            $msg = array_reduce($data, function ($carry, $value) {
                $carry .= (empty($carry) ? '' : '<br>') . (is_array($value) ? implode(',', $value) : strval($value));
                return $carry;
            });
        } else {
            $msg = strval($data);
        }
        return $msg;
    }
}

if (!function_exists('isAjaxRequest')) {

    /**
     * Check for Ajax request
     *
     * @return bool
     */
    function isAjaxRequest()
    {
        return 'xmlhttprequest' == strtolower(getVar('HTTP_X_REQUESTED_WITH')) ?? false;
    }
}

if (!function_exists('validDate')) {

    /**
     * Check for valid date
     *
     * @return mixed Return valid date string, false otherwise
     */
    function validDate($date = '')
    {
        $time = strtotime($date);
        if ($time) {
            return date('Y-m-d', $time);
        } else {
            return false;
        }
    }
}
