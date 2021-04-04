<?php

namespace Woof\Models;

use Illuminate\Database\Capsule\Manager;

class Database
{
    public static $prefix;

    private $driver;
    private $wordpressDriver;

    public function __construct($wpdb)
    {
        $this->wordpressDriver = $wpdb;
        static::$prefix = $this->wordpressDriver->prefix;

        $this->driver = new Manager();

        $this->driver->addConnection(
            [
                'driver' => 'mysql',
                'host' => \DB_HOST,
                'database' => \DB_NAME,
                'username' => \DB_USER,
                'password' => \DB_PASSWORD,
                'charset' => $this->wordpressDriver->charset,
                // 'prefix' => $this->wordpressDriver->prefix
                //'collation' => $this->wordpressDriver->collate,

            ],
            'default'
        );

        $this->driver->setAsGlobal();
        $this->driver->bootEloquent();

        /*
        $this->driver->schema()->create('bllooo', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('userimage')->nullable();
            $table->string('api_key')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
        });
        */
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function getWordpressDriver()
    {
        return $this->wordpressDriver;
    }
}
