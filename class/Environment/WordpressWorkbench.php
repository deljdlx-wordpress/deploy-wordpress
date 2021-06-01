<?php
namespace Deljdlx\Deploy\Wordpress\Environment;

use Deljdlx\Deploy\Environment;

abstract class WordpressWorkbench extends Environment
{

    abstract public function getHostname();

    public function __construct($name, $local = true)
    {
        parent::__construct($name, $local);
    }


    /**
     * @return this
     */
    public function initialize()
    {
        parent::initialize();
        $this->preInitialize();
        $this->configureRepository();
        $this->configurePathes();
        $this->configureURL();
        $this->configureDatabase();
        $this->configureWorpressOptions();
        $this->configureSaltz();
        $this->configureJWT();
        $this->postInitialize();

        return $this;
    }


    /**
     * @return this
     */
    protected function preInitialize()
    {
        $this
            ->hostname($this->getHostname())
            ->set('hostname', $this->getHostname())
        ;

        return $this;
    }


    /**
     * @return this
     */
    protected function postInitialize()
    {
        return $this;
    }


    /**
     * @return this
     */
    protected function configureRepository()
    {
        $this
            ->set('repository', 'git@github.com:deljdlx/wordpress-template.git')
            ->set('branch', 'develop')
        ;
        return $this;
    }


    /**
     * @return this
     */
    protected function configureJWT()
    {
        $this
            ->set('JWT_AUTH_SECRET_KEY', $this->generateHash())
            ->set('JWT_AUTH_CORS_ENABLE', true)
        ;

        return $this;
    }


    /**
     * @return this
     */
    protected function configureDatabase()
    {

        $this
            ->set('DB_TABLE_PREFIX', 'wp_')
            ->set('DB_CHARSET', 'utf8')
            ->set('DB_COLLATE', '')
            ->set('DB_TABLE_PREFIX', 'ww_')
            // IMPORTANT Overide this parameters in child classes
            // ->set('DB_NAME', 'CHANGE_ME')
            // ->set('DB_USER', 'CHANGE_ME')
            // ->set('DB_PASSWORD', 'CHANGE_ME')
            // ->set('DB_HOST', 'CHANGE_ME')
        ;
        return $this;
    }


    /**
     * @return this
     */
    protected function configurePathes()
    {
        $this
            ->set('DEPLOY_FILEPATH', getcwd())
            ->set('PUBLIC_FOLDER', 'public')
            ->set('WP_SOURCE_FOLDER', 'wordpress')
            ->set('WP_CONTENT_FOLDER', 'content')
        ;

        return $this;
    }

    /**
     * @return this
     */
    protected function configureURL()
    {
        $this
            ->set('WP_HOME', 'http://{{hostname}}/' . $this->get('PUBLIC_FOLDER') . '/')
        ;

        return $this;
    }

    /**
     * @return this
     */
    protected function configureWorpressOptions()
    {
        $this
            ->set('SITE_NAME', 'Wordpress Workbench')

            // Wordpress site configuration
            ->set('BO_USER', 'admin')
            ->set('BO_PASSWORD', 'admin')
            ->set('BO_EMAIL', 'admin@mail.com')

            ->set('FS_METHOD', 'direct')
            ->set('WP_USE_THEMES', true)
            ->set('WP_ENVIRONMENT_TYPE', 'staging')
            ->set('WP_DEBUG', true)
        ;

        return $this;
    }

    /**
     * @return this
     */
    protected function configureSaltz()
    {
        $this
            /**#@+
             * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
            */
            ->set('AUTH_KEY', $this->generateHash())
            ->set('SECURE_AUTH_KEY', $this->generateHash())
            ->set('LOGGED_IN_KEY', $this->generateHash())
            ->set('NONCE_KEY', $this->generateHash())
            ->set('AUTH_SALT', $this->generateHash())
            ->set('SECURE_AUTH_SALT', $this->generateHash())
            ->set('LOGGED_IN_SALT', $this->generateHash())
            ->set('NONCE_SALT', $this->generateHash())
        ;
        return $this;
    }

    /**
     * @return string
     */
    protected function generateHash()
    {
        return md5(uniqid()).sha1(uniqid());
    }
}
