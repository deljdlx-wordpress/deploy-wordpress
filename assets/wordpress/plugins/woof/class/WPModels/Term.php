<?php

namespace Woof\WPModels;

class Term
{

    public $term_id = null;
    public $name = null;
    public $slug = null;
    public $term_group = null;
    public $term_taxonomy_id = null;
    public $taxonomy = null;
    public $description = null;
    public $parent = null;
    public $count = null;
    public $filter = null;
    public $cat_ID = null;
    public $category_count = null;
    public $category_description = null;
    public $cat_name = null;
    public $category_nicename = null;
    public $category_parent = null;

    private $wordpressTerm;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->term_id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return get_category_link($this->getId());
    }


    /**
     * @return \WP_Term
     */
    public function getWordpressTerm()
    {
        return $this->wordpressTerm;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function loadById($id)
    {
        $wpTerm = get_term($id);
        $this->loadFromWordpressTerm($wpTerm);
        return $this;
    }

    /**
     * @param \WP_Term $wpTerm
     * @return $this
     */
    public function loadFromWordpressTerm($wpTerm)
    {
        $this->wordpressTerm = $wpTerm;
        foreach($wpTerm as $attribute => $value) {
            $this->$attribute = $value;
        }
        return $this;
    }

    /**
     * @param \WP_Term $wpTerm
     * @return static
     */
    public static function getFromWordpressTerm($wpTerm)
    {
        $term = new static();
        $term->loadFromWordpressTerm($wpTerm);
        return $term;
    }

}
