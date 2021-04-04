<?php
namespace Woof\Theme\Customizer;


class Section
{

    protected $id;
    protected $caption;
    protected $order;
    protected $description;

    protected $customizer;

    public function __construct($id, $caption, $order = 100, $description = 'Customizer')
    {
        $this->id = $id;
        $this->caption = $caption;
        $this->order = $order;
        $this->description = $description;
    }

    public function register()
    {
        add_action('customize_register', [$this, 'createSection']);
    }

    public function createSection($customizer)
    {
        $this->customizer = $customizer;
        return $this->customizer->add_section(
            $this->id,
            [
                'title' => __($this->caption), //Visible title of section
                'priority' => $this->order, //Determines what order this appears in
                'capability' => 'edit_theme_options', //Capability needed to tweak
                'description' => __($this->description), //Descriptive tooltip
            ]
        );
    }



    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of caption
     */ 
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set the value of caption
     *
     * @return  self
     */ 
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get the value of order
     */ 
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
