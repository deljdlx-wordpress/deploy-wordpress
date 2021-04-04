<?php
namespace Woof\Theme;

class Template
{


    protected $posts = [];

    public function getHeader()
    {
        wp_head();
        return $this;
    }

    public function getFooter()
    {
        wp_footer();
        return $this;
    }

    public function getPosts()
    {
        $posts = [];
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $posts[] = get_post();

            }
        }
        return $posts;
    }
}


