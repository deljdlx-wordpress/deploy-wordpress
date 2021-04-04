<?php
namespace Woof\Theme\Customizer;

use WP_Customize_Control;

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since MyTheme 1.0
 */
class Customizer
{
    const MOD_NAME = 'unamed-mod';
    const JS_CUSTOMIZER = '/assets/js/theme-customizer.js';

    protected static $customizerIndex = 0;

    /**
     * @var ThemeParameter
     */
    protected $parameter;

    protected $type = WP_Customize_Control::class;
    protected $defaultValue = null;
    protected $caption ='';

    protected $customizer;

    /**
     * @var Section
     */
    protected $section;

    protected $partialEditSelector = '';
    protected $previewUpdateCode = '';

    public function __construct(ThemeParameter $parameter, $caption = null, $partialSelector = null)
    {

        $this->parameter = $parameter;

        $this->partialEditSelector = $partialSelector;

        if($caption !== null) {
            $this->caption = $caption;
        }
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function getValue()
    {
        return $this->parameter->getValue(true);
    }


    public function setPreviewQuerySelector($selector)
    {
        $this->previewQuerySelector = $selector;
        return $this;
    }

    public function getPreviewQuerySelector()
    {
        return $this->previewQuerySelector;
    }


    public function setPreviewUpdateCode($javascript)
    {
        $this->previewUpdateCode = $javascript;
        return $this;
    }

    public function getPreviewUpdateCode()
    {
        return $this->previewUpdateCode;
    }


    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setSection($section)
    {
        $this->section = $section;
        return $this;
    }

    public function getCaption()
    {
        return  $this->caption;
    }

    public function getName()
    {
        return $this->parameter->getName();
    }

    public function getSection()
    {
        return $this->section;
    }

    public function register()
    {
        // Enqueue live preview javascript in Theme Customizer admin screen
        // add_action('customize_preview_init', [$this, 'livePreview']);
        add_action('customize_register', [$this, 'activate']);


        add_action('wp_head', [$this, 'generateCSS']);
        add_action('customize_preview_init', [$this, 'generateJS'], 100);
        return $this;
    }



    public function activate($wp_customizer)
    {
        $this->customizer = $wp_customizer;
        $this->registerSetting();
        $this->registerControl();
        $this->registerPartialEdit();
    }


    /**
     * This outputs the javascript needed to automate the live settings preview.
     * Also keep in mind that this function isn't necessary unless your settings
     * are using 'transport'=>'postMessage' instead of the default 'transport'
     * => 'refresh'
     *
     * Used by hook: 'customize_preview_init'
     *
     * @see add_action('customize_preview_init',$func)
     * @since MyTheme 1.0
     */
    public function livePreview()
    {
        wp_enqueue_script(
            $this->getName() . '-customizer', // Give the script a unique ID
            get_template_directory_uri() . static::JS_CUSTOMIZER, // Define the path to the JS file
            ['jquery', 'customize-preview'], // Define dependencies
            '', // Define a version (optional)
            true // Specify whether to put in footer (leave this true)
        );
    }


    protected function registerSetting()
    {
        $this->customizer->add_setting(
            $this->parameter->getName(), //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            [
                'default' => $this->parameter->getDefaultValue(), //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            ]
        );
    }

    protected function registerControl()
    {
        //$this->customizer->add_control(new \WP_Customize_Code_Editor_Control( //Instantiate the color control class
        $this->customizer->add_control(new $this->type( //Instantiate the color control class
            $this->customizer, //Pass the $wp_customize object (required)
            $this->parameter->getName(), //Set a unique ID for the control
            [
                'label' => __($this->getCaption()), //Admin-visible name of the control
                'settings' => $this->parameter->getName(), //Which setting to load and manipulate (serialized is okay)
                'priority' => 10, //Determines the order this control appears in for the specified section
                'section' => $this->getSection()->getId(), //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            ]
        ));
    }

    /**
     * This will output the custom WordPress settings to the live theme's WP head.
     *
     * Used by hook: 'wp_head'
     *
     * @see add_action('wp_head',$func)
     * @since MyTheme 1.0
     */
    public function generateCSS()
    {

    }

    public function generateJS()
    {

    }

    public function registerPartialEdit()
    {

        if(!$this->partialEditSelector) {
            return false;
        }

        $this->customizer->selective_refresh->add_partial(
            $this->getName(), [
                'selector' => $this->partialEditSelector,
                'container_inclusive' => true,
                'fallback_refresh' => false,  // Prevents refresh loop when document does not contain selector
                /*
                'render_callback' => function() {
                    return '
                    :root {
                        --color-highlight-00: ' . get_theme_mod(static::MOD_NAME) . ';
                    }
                    ';
                }
                */
            ]
        );

    }




}
