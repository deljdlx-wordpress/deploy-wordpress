<?php
namespace Woof\Theme\Customizer;


class ThemeParameter
{
    protected $name;
    protected $defaultValue;
    protected $value;

    public function __construct($name, $defaultValue = '')
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->value =  get_theme_mod($this->name);
    }


    /**
     * Get the value of defaultValue
     */ 
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set the value of defaultValue
     *
     * @return  self
     */ 
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue($getDefault = false)
    {
        if($getDefault && ($this->value === null || $this->value === false)) {
            return $this->getDefaultValue();
        }
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
