<?php
namespace Woof\Routing;


class CustomRouter extends \AltoRouter
{


    /**
     * @var Route[]
     */
    private $customRoutes = [];

    /**
     * @var CustomRouter
     */
    private static $instance;

    /**
     * @var string
     */
    protected $basePath;


    /**
     * get the main router instance
     *
     * @return CustomRouter
     */
    public static function getInstance()
    {
        if(!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $this->basePath = $basePath;
        $this->setBasePath($basePath);
        static::$instance = $this;
    }


    /**
     * @param [type] $route
     * @return $this
     */
    public function addRoute($route)
    {
        $this->customRoutes[$route->getName()] = $route;
        return $this;
    }



    /**
     * launch the routing
     *
     * @return mixed
     */
    public function route()
    {

        foreach($this->customRoutes as $name => $route) {

            $pattern = $route->getPattern();

            $this->map(
                $route->getMethod(),
                $pattern,
                $route->getCallback(),
                $name
            );
        }

        $match = $this->match();


        if($match['target']) {
            $closure = $match['target'];
            return call_user_func_array($closure, $match['params']);
        }
        else {
            throw new \Exception('No route found');
        }
    }
}
