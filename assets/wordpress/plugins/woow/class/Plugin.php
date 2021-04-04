<?php

namespace Woow;

use Woof\WPModels\Post;
use Woof\Plugin as WoofPlugin;
use Woof\WPModels\Role;
use Woof\WPModels\User;

class Plugin extends WoofPlugin
{


    public function __construct($filepath)
    {
        parent::__construct($filepath);


        $this->addRoute(
            'GET',
            'woow/home',
            '/woow/home/[i:id]/?',
            function($id) {


                $post = new Post();
                $post->loadById(1);
                echo '<div style="border: solid 2px #F00">';
                    echo '<div style="; background-color:#CCC">@'.__FILE__.' : '.__LINE__.'</div>';
                    echo '<pre style="background-color: rgba(255,255,255, 0.8);">';
                    print_r($post);
                    echo '</pre>';
                echo '</div>';

                $user = new User();
                $user->loadById(1);

                echo '<div style="border: solid 2px #F00">';
                    echo '<div style="; background-color:#CCC">@'.__FILE__.' : '.__LINE__.'</div>';
                    echo '<pre style="background-color: rgba(255,255,255, 0.8);">';
                    print_r($user->getRoles());
                    echo '</pre>';
                echo '</div>';

                $user->addRole('editor');

                $role = new Role();
                $role->loadByName('administrator');
                echo '<div style="border: solid 2px #F00">';
                    echo '<div style="; background-color:#CCC">@'.__FILE__.' : '.__LINE__.'</div>';
                    echo '<pre style="background-color: rgba(255,255,255, 0.8);">';
                    print_r($role);
                    echo '</pre>';
                echo '</div>';


                echo '<div style="border: solid 2px #F00">';
                    echo '<div style="; background-color:#CCC">@'.__FILE__.' : '.__LINE__.'</div>';
                    echo '<pre style="background-color: rgba(255,255,255, 0.8);">';
                    print_r($user->is('administrator'));
                    echo '</pre>';
                echo '</div>';

                echo '<div style="border: solid 2px #F00">';
                    echo '<div style="; background-color:#CCC">@'.__FILE__.' : '.__LINE__.'</div>';
                    echo '<pre style="background-color: rgba(255,255,255, 0.8);">';
                    print_r($user->getName());
                    echo '</pre>';
                echo '</div>';

                echo '<div style="border: solid 2px #F00">';
                    echo '<div style="; background-color:#CCC">@'.__FILE__.' : '.__LINE__.'</div>';
                    echo '<pre style="background-color: rgba(255,255,255, 0.8);">';
                    print_r($id);
                    echo '</pre>';
                echo '</div>';
                echo 'hello world';
            },
            'woow-home'
        );
    }
}
