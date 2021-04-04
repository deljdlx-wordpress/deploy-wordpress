<?php

namespace Woof\WPModels;

use WP_Roles;

class Role
{

    /**
    * @var string
    */
    protected $name;

    /**
    * @var string
    */
    protected $label;

    protected $capabilities = null;

    /**
     * @var \WP_Role
     */
    private $wordpressRole;

    /**
     * @var WP_Roles
     */
    private $driver;


    public function __construct($name = null, $label = null)
    {
        $this->driver = new WP_Roles();
        $this->name = $name;
        $this->label = $label;
    }

    public function loadByName($name)
    {
        $role = $this->driver->get_role($name);
        $this->name = $role->name;
        $this->wordpressRole = $role;

        $this->capabilities = &$this->wordpressRole->capabilities;

        return $this;
    }

    public function setCapability($capabilityName, $value)
    {
        $this->capabilities[$capabilityName] = $value;
        return $this;
    }

    public function delete()
    {
        //DOC https://developer.wordpress.org/reference/functions/remove_role/
        remove_role($this->name);
        return $this;
    }

    public function getWordpressRole()
    {
        if(!$this->wordpressRole) {
            $this->loadByName($this->name);
        }
        return $this->wordpressRole;
    }

    public function getCapabilities()
    {
        return $this->getWordpressRole()->capabilities;
    }

    // =======================================================================
    public function register()
    {
        $this->wordpressRole = add_role(
            $this->name,
            $this->label,
            $this->capabilities
        );

        return $this;
    }
}
