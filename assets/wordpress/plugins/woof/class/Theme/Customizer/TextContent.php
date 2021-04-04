<?php

namespace Woof\Theme\Customizer;

class TextContent extends StringVariable
{

    public function register()
    {
        parent::register();
        // add_action('wp_head', [$this, 'generateCSS']);
        // add_action('wp_footer', [$this, 'generateJS'], 100);
    }


    /**
     * This hooks into 'customize_register' (available as of WP 3.4) and allows
     * you to add new sections and controls to the Theme Customize screen.
     *
     * Note: To enable instant preview, we have to actually write a bit of custom
     * javascript. See live_preview() for more.
     *
     * @see add_action('customize_register',$func)
     * @param \WP_Customize_Manager $wp_customize
     * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
     * @since MyTheme 1.0
     */

     /*
    public function hookRegister($wp_customizer)
    {
        parent::hookRegister($wp_customizer);


        $this->customizer->selective_refresh->add_partial(
            $this->getName(), [
                'selector' => '.theme-mod-'.static::MOD_NAME,
                'container_inclusive' => true,
                'fallback_refresh' => false,  // Prevents refresh loop when document does not contain selector

            'render_callback' => function() {
                return '
                :root {
                    --color-highlight-00: ' . get_theme_mod(static::MOD_NAME) . ';
                }
                ';
            }
            ]
        );
    }
    */


}
