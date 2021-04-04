<?php
namespace Woof\Routing;

use Woof\Routing\CustomRouter;
use Woof\Plugin;

class Router
{

    const WOOF_ROUTE_PARAMETER = 'woof-custom-route';

    protected $plugin;
    protected $routes = [];

    protected $customerRouter;


    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->customRouter = new CustomRouter();
    }

    public function getFilepath()
    {
        return $this->plugin->getFilepath();
    }

    public function addRoute($route) {

        // $method, $pattern, $callback , $name = null
        // $route = new Route($method, $pattern, $callback , $name);
        $this->routes[] = $route;
        return $this;
    }


    public function route($wp)
    {
        if(isset($wp->query_vars[static::WOOF_ROUTE_PARAMETER])) {
            return $this->customRouter->route();
        }
    }

    public function registerRoutes()
    {
        foreach($this->routes as $route) {
            // https://developer.wordpress.org/reference/functions/add_rewrite_rule/
            // DOC regexp http://www.expreg.com/presentation.php
            add_rewrite_rule(
                $route->getWordpressRegexp(),
                'index.php?'. static::WOOF_ROUTE_PARAMETER . '=1',  // vers quel "format virtuel" wordpress va transformer l'url demandée
                'top'   // la route se mettra en haut de la pile de priorités des routes enregistrées par wordpress
            );
            $this->customRouter->addRoute($route);
        }

        // WARNING penser à retirer ceci dans la vrai vie
        $this->flushRoutes();

        // ce hook permet à wordpress de savoir quel fichier il va utiliser en tant que template
        add_action('template_include', [$this, 'displayTemplate']);
        add_action('parse_request', [$this, 'route']);
    }

    // le paramètre $template est le template que wordpress compte utiliser
    public function displayTemplate($template)
    {
        // DOC https://developer.wordpress.org/reference/functions/get_query_var/
        $customRoute = get_query_var(static::WOOF_ROUTE_PARAMETER);
        if(!empty($customRoute)) {
            return false;
        }
        return $template;
    }


    public function flushRoutes()
    {
        flush_rewrite_rules();
    }

    // ==============================================================
    public function register()
    {
        add_filter('query_vars', function ($query_vars) {
            $query_vars[] = static::WOOF_ROUTE_PARAMETER;
            return $query_vars;
        });
        add_action('init', [$this, 'registerRoutes']);
    }

}
