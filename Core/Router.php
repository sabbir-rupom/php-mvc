<?php

namespace Core;

/**
 * Router class
 */
class Router
{

    /**
     * Associative array of routes (the routing table)
     * @var array
     */
    protected $routes = [];

    /**
     * Allowed methods in routing
     * @var array
     */
    protected $httpMethods = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    /**
     * Current class name
     * @var string
     */
    protected $class = '';

    /**
     * Current method name
     *
     * @var string
     */
    protected $method = '';

    /**
     * Add a route to the routing table
     *
     * @param string $route  The route URL
     * @param string $destination String syntax denoting controller with method e.g App\Controller\Home@index
     * @param array $httpMethods Allowed HTTP methods
     *
     * @return void
     */
    public function add(string $route, string $destination, array $httpMethods = [])
    {
        $this->routes[$route] = $destination;
        $this->httpMethods[$route] = array_map('strtoupper', $httpMethods);
    }

    /**
     * Get all the routes from the routing table
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the $params
     * property if a route is found.
     *
     * @param string $uri The route URL
     *
     * @return boolean  true if a match found, false otherwise
     */
    public function match($uri): bool
    {
        if (strpos($uri, '/public/') !== false) {
            $uri = str_replace('/public', '', substr($uri, strpos($uri, '/public')));
        }

        if ($uri === '/' && array_key_exists('/', $this->routes)) {
            $action = explode('@', $this->routes['/']);
            if (isset($action[1]) === false) {
                $action[1] = 'index';
            }

            $this->class = $action[0];
            $this->method = $action[1];

            return true;
        } else {
            foreach ($this->routes as $key => $val) {
                // Convert wildcards to RegEx
                $request = str_replace(array(':value', ':num'), array('[^/]+', '[0-9]+'), $key, $count);

                // Does the RegEx match?
                if (preg_match('#^' . $request . '$#', $uri, $matches)) {
                    if ($this->checkHttpMethod($key) === false) {
                        return false;
                    }

                    $segments = explode('/', $uri);

                    $this->params = array_slice($segments, (count($segments) - $count));

                    $action = explode('@', $val);

                    if (isset($action[1]) === false) {
                        $action[1] = 'index';
                    }

                    $this->class = $action[0];
                    $this->method = $action[1];

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check requested route path has eligible HTTP method
     *
     * @param string $route Requested route URI
     * @return bool
     */
    public function checkHttpMethod(string $route): bool
    {
        if (!isset($this->httpMethods[$route]) || empty($this->httpMethods[$route])) {
            return true;
        } elseif (in_array(getMethod(), $this->httpMethods[$route])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Dispatch the route, creating the controller object and running the
     * action method
     *
     * @param string $url The route URL
     *
     * @return void
     */
    public function dispatch()
    {
        $url = requestUri();

        if ($this->match($url)) {
            if (class_exists($this->class)) {
                $action = new $this->class();

                if (method_exists($action, $this->method)) {
                    call_user_func_array(array($action, $this->method), $this->params);
                } else {
                    throw new \Exception("Method {$this->method} does not exist in controller {$this->class} thus cannot be called");
                }
            } else {
                throw new \Exception("Controller class {$this->class} not found");
            }
        } else {
            throw new \Exception('No route matched.', 404);
        }
    }
}
