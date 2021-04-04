<?php
namespace Woof\Theme;


use Woof\Theme\Customizer\Section;
use Woof\Theme\Customizer\ThemeParameter;

use function Woof\slugify;

class Skeleton
{
    protected $css = [];

    protected $view;
    protected $model;

    // https://developer.wordpress.org/reference/functions/add_theme_support/
    protected $features = [
        'title-tag',
        'post-thumbnails',
        'menus'
    ];

    protected $parameters = [];

    protected $customizerSections = [];

    protected $customizers = [];

    //===========================================================
    //
    //===========================================================
    public static function getInstance()
    {
        return $GLOBALS['WOOF_THEME'];
    }

    public function __construct()
    {
        $this->view = new Template();
        $this->model = new Loop();
    }

    public function getView() {
        return $this->view;
    }

    public function getModel() {
        return $this->model;
    }

    public function partial($slug, $name = null, $data = [])
    {
        if($name === null) {
            $name = slugify($slug);
        }

        // source file : public\wp\wp-includes\general-template.php
        do_action( "get_template_part_{$slug}", $slug, $name, $data );

        $templates = array();
        $name      = (string) $name;
        if ( '' !== $name ) {
            $templates[] = "{$slug}-{$name}.php";
        }

        $templates[] = "{$slug}.php";
        do_action( 'get_template_part', $slug, $name, $templates, $data );

        $template = $this->locateTemplate( $templates, true, false, $data );

        if($template) {
            $this->loadTemplate($template);
            return $template;
        }
        else {
            return false;
        }
    }

    public function locateTemplate($template_names, $load = false, $require_once = true, $data = array())
    {
        $located = '';
        foreach ( (array) $template_names as $template_name ) {
            if ( ! $template_name ) {
                continue;
            }
            if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
                $located = STYLESHEETPATH . '/' . $template_name;
                break;
            } elseif ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
                $located = TEMPLATEPATH . '/' . $template_name;
                break;
            } elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
                $located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
                break;
            }
        }
        return $located;
    }

    public function loadTemplate($_template_file, $require_once = true, $data = array(), $extract = true)
    {
        // source file : public\wp\wp-includes\general-template.php
        global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

        if(!array_key_exists('theme', $data)) {
            $data['theme'] = $this;
        }

        if ( is_array( $wp_query->query_vars ) ) {
            extract( $wp_query->query_vars, EXTR_SKIP );
        }

        if ( isset( $s ) ) {
            $s = esc_attr( $s );
        }

        if($extract) {
            extract($data);
        }

        if ( $require_once ) {
            require_once $_template_file;
        } else {
            require $_template_file;
        }
    }


    public function register()
    {
        $GLOBALS['WOOF_THEME'] = $this;

        $this->enableFeatures();
        $this->registerParameters();

        $this->registerCustomizersSections();
        $this->registerCustomizers();

        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        add_action( 'customize_preview_init', [$this, 'registerCustomizerAssets']);
    }


    public function registerCustomizerAssets()
    {
        return $this;
    }



    public function registerAssets()
    {
        return $this;
    }


    public function registerCustomizersSections()
    {
        foreach($this->customizerSections as $sectionId => &$descriptor) {
            $section = new Section(
                $sectionId,
                $descriptor['caption'],
                $descriptor['order'],
                $descriptor['description']
            );
            $this->customizerSections[$sectionId]['instance'] = $section;
            $section->register();
        }

        return $this;
    }

    public function getParameters()
    {
        $parameters = [];
        foreach($this->parameters as $index => $descriptor) {
            $parameters[$index] = $descriptor['instance'];
        }
        return $parameters;
    }

    public function getParameter($name)
    {
        return $this->parameters[$name]['instance'];
    }

    public function registerParameters()
    {


        foreach($this->parameters as $paramenterName => $descriptor) {
            $parameter = new ThemeParameter($paramenterName, $descriptor['defaultValue']);
            $this->parameters[$paramenterName]['instance'] = $parameter;
        }

        return $this;
    }


    protected function registerCustomizers()
    {
        foreach($this->customizers as $parameterName => $descriptor) {
            $control = $descriptor['type'];
            $customizer = new $control(
                $this->parameters[$parameterName]['instance'],
                $descriptor['caption'],
                isset($descriptor['partialSelector']) ? $descriptor['partialSelector'] : null
            );
            $customizer->setSection($this->customizerSections[$descriptor['section']]['instance']);
            $customizer->register();
        }
        return $this;
    }



    protected function registerScript($name, $path, $dependencies = [], $version = null, $inFooter = true)
    {
        wp_enqueue_script(
            $name,
            get_theme_file_uri($path),
            $dependencies,
            $version,
            $inFooter
        );
    }



    protected function registerCSS($name, $path, $dependencies = [], $version = '1')
    {
        wp_enqueue_style(
            $name,
            get_theme_file_uri($path),
            $dependencies,
            $version
        );
    }


    protected function enableFeatures()
    {
        foreach($this->features as $feature) {
            add_theme_support($feature);
        }
    }
}