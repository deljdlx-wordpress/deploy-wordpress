<?php

namespace Deljdlx\DeployWordpress;

class WoowRecipe extends WordpressRecipe
{

    public function registerTasks()
    {
        parent::registerTasks();


        $this->setTask('installWoof', function() {
            return $this->installWoof();
        });
        $this->setTask('installWoow', function() {
            return $this->installWoow();
        });
        $this->setTask('installWoowTheme', function() {
            return $this->installWoowTheme();
        });

        $this->setTask('activateWoowTheme', function() {
            return $this->activateWoowTheme();
        });



        $this->setTask('buildWoowTheme', function() {
            return $this->buildWoowTheme();
        });

        $this->setTask('watchWoowTheme', function() {
            return $this->watchWoowTheme();
        });

        $this->setTask('buildAllWoow', function() {
            return $this->buildAllWoow();
        });
    }




    public function installWoof($force = false)
    {
        if (!$this->isDir('{{site_filepath}}/wp-content/plugins/woof') && !$force) {
            $this->clonePlugin('git@github.com:deljdlx/woof.git');
        }
        else {
            $this->echo("woof package already present");
            $this->echo("updating woof package");
            $this->updatePlugin('woof');
        }
        if (!$this->isDir('{{site_filepath}}/wp-content/plugins/woof-model') && !$force) {
            $this->clonePlugin('git@github.com:deljdlx/woof-model.git');
        }
        else {
            $this->echo("woof-model package already present");
            $this->echo("updating woof-them package");
            $this->updatePlugin('woof-theme');
        }
        if (!$this->isDir('{{site_filepath}}/wp-content/plugins/woof-theme') && !$force) {
            $this->clonePlugin('git@github.com:deljdlx/woof-theme.git');
        }
        else {
            $this->echo("woof-theme package present");
            $this->echo("updating woof-theme package");
            $this->updatePlugin('woof-theme');
        }
    }

    public function installWoow($force = false)
    {

        $this->installWoof($force);
        if (!$this->isDir('{{site_filepath}}/wp-content/plugins/woow') && !$force) {
            return $this->clonePlugin('git@github.com:deljdlx/woow.git');
        }
    }

    public function installWoowTheme($force = false)
    {
        $this->installWoow($force);
        if (!$this->isDir('{{site_filepath}}/wp-content/themes/woow-theme') && !$force) {
            // $this->upload(__DIR__ . '/../assets/wordpress/themes/woow-theme', '{{site_filepath}}/wp-content/themes');

            $this->cd('{{site_filepath}}/wp-content/themes');
            $this->run('git clone git@github.com:deljdlx/woow-theme.git', [
                'tty' => true
            ]);

            /*
            $this->cd('{{site_filepath}}/wp-content/themes/woow-theme');
            $this->run('composer install', [
                'tty' => true
            ]);
            */
        }

        $this->cd('{{site_filepath}}/wp-content/themes/woow-theme/assets/vuejs');
        $this->run('npm install', [
            'tty' => true
        ]);
        $this->buildWoowTheme();
        $this->activateWoowTheme();
    }

    public function buildWoowTheme()
    {
        $this->cd('{{site_filepath}}/wp-content/themes/woow-theme/assets/vuejs');
        $this->run('vue build src/main.js', [
            'tty' => true
        ]);
    }

    public function activateWoowTheme()
    {
        $this->cd('{{site_filepath}}');
        return $this->run('wp theme activate woow-theme', [
            'tty' => true
        ]);
    }


    public function buildAllWoow($force = false)
    {
        $this->installWoof($force);
        $this->installWoow($force);
        $this->installWoowTheme($force);
        $this->buildWoowTheme();
        $this->activateWoowTheme();
    }




    public function watchWoowTheme()
    {
        $this->cd('{{site_filepath}}/wp-content/themes/woow-theme/assets/vuejs');
        $this->run('vue serve src/main.js', [
            'tty' => true
        ]);

    }



    public function installRequirements()
    {
        parent::installRequirements();
        $this->installVue();
    }



    public function installVue()
    {
        $this->run('npm install -g @vue/cli', [
            'tty' => true
        ]);

        $this->run('npm i -g @vue/cli-service-global', [
            'tty' => true
        ]);

    }



}