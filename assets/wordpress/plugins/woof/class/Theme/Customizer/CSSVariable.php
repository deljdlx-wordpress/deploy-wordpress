<?php

namespace Woof\Theme\Customizer;
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/
 * @since MyTheme 1.0
 */

// Stsf\Customizer\HomeMainImage
// Stsf\Customizer\HomeMainImage

class CSSVariable extends StringVariable
{


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
        echo '<!--Customizer CSS-->' . PHP_EOL;
        echo '<style type="text/css" class="theme-mod-' . $this->getName() . '">' . PHP_EOL;
        echo '
        :root {
            --' . $this->getName() . ': ' . $this->getValue() . ';
        }
        ';
        echo '</style>' . PHP_EOL;
        echo '<!--/Customizer CSS-->' . PHP_EOL;


    }

    public function generateJS()
    {
        echo '<script>' . PHP_EOL;
        echo 'document.addEventListener("DOMContentLoaded", function() {' . PHP_EOL;
            echo 'wp.customize( "' . $this->getName() . '", function( value ) {' . PHP_EOL;
                echo 'value.bind( function( newValue ) {' . PHP_EOL;
                    echo "document.querySelector('.theme-mod-" . $this->getName() . "').innerHTML = ':root {--" . $this->getName() . ": ' + newValue+ ';}';"  . PHP_EOL;
                echo "});" . PHP_EOL;
            echo "});" . PHP_EOL;
        echo "});" . PHP_EOL;
        echo '</script>' . PHP_EOL;
    }
}
