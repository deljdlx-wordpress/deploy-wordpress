<?php
namespace Woof\Theme;

use Woof\WPModels\Post;

class Loop
{


    protected $posts = [];

    public function getPosts()
    {
        $posts = [];
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $wpPost = get_post();
                $post = new Post();
                $post->loadFromWordpressPost($wpPost);
                $posts[] = $post;

            }
        }
        return $posts;
    }
}


