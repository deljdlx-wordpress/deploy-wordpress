<?php

namespace Woof\Content;

// https://wpsmith.net/2019/custom-rewrite-rules-custom-post-types-taxonomies/
// https://developer.wordpress.org/reference/functions/register_post_type/
class CustomPostType
{
    protected $name;
    protected $label;

    /*
    Positions for Core Menu Items
    2 Dashboard
    4 Separator
    5 Posts
    10 Media
    15 Links
    20 Pages
    25 Comments
    59 Separator
    60 Appearance
    65 Plugins
    70 Users
    75 Tools
    80 Settings
    99 Separator
    */


    protected $options = [
        'label' => 'Custom post type',

        // optionnel ; aller voir la doc pour avoir des exemple d'utilisation
        //'labels' => [],

        'description' => 'Custom post type',

        'menu_position' => 4,
        'menu_icon' => 'dashicons-location-alt',

        // est ce que les contenus gèrent le fait qu'ils ont un parent
        'hierarchical' => false,

        // le custom post type sera éditable depuis le bo
        'public' => true,

        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,

        'can_export' => true,

        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,

        // cet index permet de gérer les droits (acl)
        // lorsque la valeur vaut post ; le "cpt" utilisera les même droits que ceux appliqués sur la gestion des "posts"
        'capability_type' => 'post',

        // attention active Gutenberg  ! ; il faudra que l'on gère la désactivation de gutenberg manuellement
        'show_in_rest' => true,

        // attention à ne surtout pas oublier cette ligne ; si custom capabilitie + Gutenberg
        // 'map_meta_cap' => true,

        'supports' => [
            'title',
            'editor',
            // 'excerpt',
            'author',
            'thumbnail',
            // 'trackbacks',
            // 'custom-fields',
            // 'comments',
            // 'revisions',
            // 'page-attributes',
            // 'post-formats' // https://wordpress.org/support/article/post-formats/
        ],
    ];


    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;

    }

    public function register()
    {
        // création du post type
        add_action('init', [$this, 'registerPostType']);

        // désactivation de gutenberg
        // 10 représente la priorité de la fonction; 10 souvent valeur par défaut
        // 2 représente le nombre de paramètre que wordpress va récupéré
        add_filter('use_block_editor_for_post_type', [$this, 'disableGutemberg'], 10, 2);

        // on donne à l'administateur les droits sur le custom post type
        add_action('admin_init', [$this, 'addCapabilitiesToAdmin']);
    }

    public function registerPostType()
    {
        // register_post_type est une méthode "native de wordpress
        // https://developer.wordpress.org/reference/functions/register_post_type/
        register_post_type($this->name, $this->getOptions());
    }

    public function addCapabilitiesToRole($roleName)
    {
        // récupération du rôle
        // https://developer.wordpress.org/reference/functions/get_role/
        $role = get_role( $roleName );

        // ajout des autorisations au rôle
        $role->add_cap( 'edit_' . $this->name);
        $role->add_cap( 'edit_' . $this->name . 's');
        $role->add_cap( 'read_' .  $this->name . 's' );

        $role->add_cap( 'delete_' .  $this->name);

        $role->add_cap( 'delete_' .  $this->name . 's' );
        $role->add_cap( 'delete_others_' .  $this->name . 's' );
        $role->add_cap( 'delete_published_' .  $this->name . 's' );

        $role->add_cap( 'edit_others' .  $this->name . 's' );
        $role->add_cap( 'publish_' .  $this->name . 's' );
        $role->add_cap( 'read_private_' .  $this->name . 's' );

    }

    public function addCapabilitiesToAdministrator()
    {
        return $this->addCapabilitiesToRole('administrator');
    }

    public function setOption($optionName, $value)
    {
        $this->options[$optionName] = $value;
        return $this;
    }

    public function getOptions()
    {
        $arguments = $this->options;
        $arguments['label'] = $this->label;

        // force le route pour l'api rest
        // $arguments['rewrite']['slug'] = $this->name;


        $arguments['capability_type'] =  $this->name;


        // à ne pas oublier si utilisation de gutemberg + custom capabilities ! ; permet à wp de faire la bonne association des droits entre les droits par défaut et les droits custom
        /*
        $arguments['capabilities'] = [
            'edit_post' => 'edit_' . $this->name,
            'edit_posts' => 'edit_' . $this->name .'s',
            'edit_others_posts' => 'edit_others_' .$this->name . 's',
            'publish_posts' => 'publish_' . $this->name . 's',
            'read_post' => 'read_' . $this->name,
            'read_private_posts' => 'read_private_' . $this->name . 's',
            'delete_post' => 'delete_' . $this->name
        ];
        */
        return $arguments;
    }

    public function disableGutemberg($isGutenbergEnable, $postType)
    {
        if($postType === $this->name) {
            return false;
        }
        else {
            return $isGutenbergEnable;
        }
    }
}
