<?php

namespace Woof\WPModels;

abstract class Model
{
    // propriété qui va nous permettre de communiquer avec la bdd (un espèce de pdo)
    protected $database;


    abstract public static function getTableName();
    abstract public static function getCreateTableQuery();

    // abstract public static function getPrimaryKey();
    public static function getPrimaryKey()
    {
        return 'id';
    }


    public function __construct()
    {
        // nous allons avoir besoin d'un composant pour communiquer avec la bdd ; nous utilisons la méthode (statique) getDatabase pour récupérer l'objet wp permettant de travaller sur la bdd

        // +- kifkif mais peut poser problème "late static binding"
        // https://www.php.net/manual/en/language.oop5.late-static-bindings.php
        // $this->database = self::getDatabase();
        // $this->database = CoreModel::getDatabase();
        $this->database = static::getDatabase();
    }


    /**
     * Generic setter
     *
     * @param string $attribute
     * @param mixed $value
     * @return CoreModel
     */
    public function set($attribute, $value)
    {
        $this->$attribute = $value;
        return $this;
    }


    public function loadById($id)
    {
        $tableName = static::getTableName();
        $database = static::getDatabase();

        $primaryKey = static::getPrimaryKey();

        $sql = "
            SELECT * FROM {$tableName}
            WHERE `{$primaryKey}`=%d
        ";

        $preparedQuery = $database->prepare($sql, [$id]);
        $results = $database->get_results($preparedQuery);

        // on récupère la première ligne de résultats
        $firstResult = array_shift($results);

        // pour chaque colonne du résultat (attention le résultat est forme d'un objet "standart"), nous renseignons la propriété correspondante de notre instance
        if(!empty($firstResult)) {
            foreach($firstResult as $columnName => $value) {
                $this->set($columnName, $value);
            }
        }
    }

    /**
     * Select all row in a tabble
     * @return \stdClass[];
     */
    public static function findAll()
    {
        $tableName = static::getTableName();
        $database = static::getDatabase();

        $sql = "SELECT * FROM {$tableName}";
        // pas besoin du prepare car pas de paramètre dans la requête
        // $preparedQuery = $database->prepare($sql, []);
        $results = $database->get_results($sql);

        // ce tableau stocke la liste des objets Experience
        $resultObjects = [];
        foreach($results as $result) {
            $resultObject = new static();

            // $result est un object "standard". Nous pouvons parcourir les propriétés de l'oject comme si c'était un tableau
            foreach($result as $property => $value) {
                $resultObject->set($property, $value);
            }

            $resultObjects[] = $resultObject;
        }
        return $resultObjects;
    }



    /**
     * Execute an update by id query
     *
     * @param int $id
     * @param array $data
     * @return void
     */
    public static function update($id, array $data)
    {
        $primaryKey = static::getPrimaryKey();

        // DOC https://developer.wordpress.org/reference/classes/wpdb/update/
        $database = static::getDatabase();
        $database->update(
            static::getTableName(),
            $data,
            [$primaryKey => $id]
        );
    }

    /**
     * Delete a row in a table by id
     *
     * @param int $id
     * @return void
     */
    public static function delete($id)
    {
        $tableName = static::getTableName();
        $primaryKey = static::getPrimaryKey();

        // DOC https://www.php.net/sprintf
        $sql = "
            DELETE FROM `{$tableName}`
            WHERE `{$primaryKey}`=%d
        ";

        // récupération de l'obget global $wpdb
        $database = static::getDatabase();

        // préparation de la requête ; il faut passer les valeurs à injecter dans la requête
        $preparedQuery = $database->prepare(
            $sql, [
                // les paramètres de la requête doivent respecter l'ordre d'apparition des %* dans la requête
                $id
            ]
        );
        // execution de la requête
        $database->query($preparedQuery);
    }

    /**
     * Execute an insert query
     *
     * @param array $data
     * @return void
     */
    public static function insert(array $data)
    {
        $database = static::getDatabase();
        // DOC https://developer.wordpress.org/reference/classes/wpdb/insert/
        $database->insert(
            static::getTableName(),
            $data
        );
    }

    /**
     * Create table into BDD
     *
     * @return string
     */
    public static function createTable()
    {
        $database = static::getDatabase();

        // récupèration du charset (alphabet) utilisé par la bdd sur laquelle tourne wp
        // $database->prefix nous permet de récupérer le préfixe des tables wp
        $charset = $database->get_charset_collate();

        $sql = static::getCreateTableQuery();
        $sql = $sql . $charset . ';' ;

        static::executeCreateTableQuery($sql);
        return static::getTableName();
    }

    /**
     * Return wordpress table prefix
     * @return string
     */
    public static function getTablePrefix()
    {
        $database = static::getDatabase();
        return $database->prefix;
    }

    /**
     * Return Wordpress database component
     * @return \wpdb
     */
    public static function getDatabase()
    {
        // attention avec wp il y a du code un vieux ; il faut avoir du respect les vieux

        // cette variable globale nous permet d'accéder au composant BDD de wordpress
        global $wpdb;
        return $wpdb;
    }

    /**
     * execute a create table query
     *
     * @param string $sql
     * @return void
     */
    public static function executeCreateTableQuery($sql)
    {
        // nous devons un require à la main de cette bibliothèque afin de pouvoir utiliser la fonction dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // https://developer.wordpress.org/reference/functions/dbdelta/
        dbDelta($sql);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function dropTable()
    {
        $tableName = static::getTableName();
        $sql = "
            DROP TABLE {$tableName}
        ";
        static::execute($sql);
    }

    /**
     * @param string $sql
     * @param array $parameters
     * @return array
     */
    public static function queryAndFetch($sql, $parameters = [])
    {
        $database = static::getDatabase();
        $preparedQuery = $database->prepare($sql, $parameters);
        $results = $database->get_results($preparedQuery);
        return $results;
    }

    public static function execute($sql, $parameters = null)
    {
        $database = static::getDatabase();
        if($parameters === null) {
            return $database->query($sql);
        }
        else {
            $preparedQuery = $database->prepare($sql, $parameters);
            return $database->query($preparedQuery);
        }
    }
}
