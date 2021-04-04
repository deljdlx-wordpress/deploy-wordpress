<?php
// cette classe va nous permettre de gérer notre plugin

namespace Woof;

use Woof\Models\Database;
use Woof\Routing\Route;

class Plugin
{

    protected static $instance;

    protected $filepath;

    /**
     * @var WordpressRouter
     */
    protected $router;

    /**
     * @var CustomPostType[]
     */
    protected $customTypes = [];

    /**
     * @var CustomTaxonomy[]
     */
    protected $customTaxonomies = [];

    /**
     * @var PostMetadata[]
     */
    protected $postMetadatas = [];


    protected $roles = [];

    protected $routes = [];


    protected $database;


    public function __construct($filepath)
    {

        $this->filepath = $filepath;

        global $wpdb;
        $this->database = new Database($wpdb);


        $this->registerRouter();

        $this->registerCustomPostTypes();
        $this->registerPostMetadatas();

        $this->registerCustomTaxonomies();
        $this->registerTaxonomiesMetadata();

        $this->registerCustomRoles();

        $this->registerUserMetadata();
    }

    public static function getInstance($filepath)
    {
        if(static::$instance === null) {
            static::$instance = new static($filepath);
        }
        return static::$instance;
    }


    public function registerUserMetadata() {}
    public function registerCustomPostTypes() {}
    public function registerPostMetadatas() {}
    public function registerCustomTaxonomies() {}
    public function registerTaxonomiesMetadata() {}
    public function registerCustomRoles() {}


    /**
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }


    //===============================================================================

    protected function registerCustomPostType($name, $label, $class = CustomPostType::class)
    {
        $customType = new $class($name, $label);
        $customType->register();
        $this->customTypes[$name] = $customType;

        return $customType;
    }


    protected function registerCustomTaxonomy($name, $label, array $postTypes, $class = CustomTaxonomy::class)
    {
        $customTaxonomy = new $class($name, $label, $postTypes);
        $customTaxonomy->register();
        $this->customTaxonomies[$name] = $customTaxonomy;
        return $customTaxonomy;
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $postType
     * @param string $class
     * @return PostMetadata
     */
    protected function registerPostMetadata($name, $label, $postType, $class = PostMetadata::class)
    {
        $postMetadata = new $class(
            $postType, // le custom post type  sur lequel ajouter le champs supplémentaire
            $name, // l'identifiant (la variable) qui va nous nous permettre de stocker l'information
            $label // libéllé
        );
        $postMetadata->register();
        $this->postMetadatas[$name] = $postMetadata;
        return $postMetadata;
    }

    protected function registerRole($name, $label, $class = CustomRole::class)
    {
        $role = new $class($name, $label);
        $role->register();
        $this->roles[] = $role;
        return $role;
    }

    //===============================================================================

    /**
     * @return $this
     */
    public function registerRouter()
    {
        $this->router = new \Woof\Routing\Router($this);
        $this->router->register();
        return $this;
    }


    public function addRoute($method, $regexWordpress, $patternCustomRouter, $callback, $name = null)
    {
        $route = new Route($method, $regexWordpress, $patternCustomRouter, $callback, $name);
        $this->router->addRoute($route);
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return $this
     */
    public function route()
    {
        $this->router->route();
        return $this;
    }

    /**
     * @return String
     */
    public function getFilepath()
    {
        return $this->filepath;
    }


    //===============================================================================
    // méthodes utilitaires
    //===============================================================================

    // appelé lorsque le plugin est désactivé
    /**
     * @return $this
     */
    public function deactivate()
    {
        $this->flushRoutes();
        return $this;
    }


    // appelé lorsque le plugin est activé
    /**
     * @return $this
     */
    public function activate()
    {
        return $this;
    }

    // appelé lors de la désinstallation du plugin ⚠️ Attention cette méthode doit être statique (obligation wordpress)
    public static function uninstall()
    {
    }

    /**
     * @return $this
     */
    public function flushRoutes()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        return $this;
    }

    //===========================================================
    /**
     * @return $this
     */
    public function register()
    {
        // https://developer.wordpress.org/reference/functions/register_activation_hook/
        // enregistrement du hook qui se déclenche au moment de l'activation du plugin
        // lorsque le plugin sera activé, wp appelera la méthode activate() de l'objet $plugin (syntaxe "callable")

        register_activation_hook(realpath(__FILE__ . '/..'), [$this, 'activate']);
        register_deactivation_hook(realpath(__FILE__ . '/..'), [$this, 'deactivate']);
        register_uninstall_hook(realpath(__FILE__ . '/..'), [static::class, 'uninstall']);

        return $this;
    }

}
