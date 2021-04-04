<?php

namespace Woof\Content;


class CustomTaxonomy
{

    protected $name;
    protected $label;

    // liste des postTypes sur lesquel  la taxonomie est applicable
    protected $postTypes = [];

    protected $options = [
        'hierarchical' => false,
        'label' => '',
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'embeddable' => true,
        'rewrite' => [
            'slug' => ''
        ],
    ];

    public function __construct($name, $label, array $postTypes)
    {
        $this->postTypes = $postTypes;
        $this->name = $name;
        $this->label = $label;

        $this->options['label'] = $label;
        $this->options['rewrite']['slug'] = $this->name;
    }


    public function setOption($optionName, $value)
    {
        $this->options[$optionName] = $value;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function registerTaxonomy()
    {
        // DOC https://developer.wordpress.org/reference/functions/register_taxonomy/
        register_taxonomy(
            $this->name,
            $this->postTypes,
            $this->options
        );
    }

    // ===============================================================================================

    public function register()
    {
        add_action('init', [$this, 'registerTaxonomy']);
    }
}
