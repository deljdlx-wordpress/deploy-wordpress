<?php

namespace Woof\WPModels;

class Category extends Term
{

    protected $parentCategories;

    /**
     * @return false|Category
     */
    public function getParent()
    {
        $parents = $this->getParents();
        reset($parents);
        return current($parents);
    }


    /**
     * @return Category[]
     */
    public function getParents()
    {
        if($this->parentCategories === null) {
            $this->parentCategories = [];

            $parents = get_ancestors($this->getId(), 'category', 'taxonomy');

            foreach($parents as $parent) {

                $wpCategory = get_term($parent);
                $this->parentCategories[] = static::getFromWordpressTerm($wpCategory);
            }
        }
        return$this->parentCategories;
    }


    /**
     * @return Category
     */
    public function getWordpressCategory()
    {
        return parent::getWordpressTerm();
    }


}
