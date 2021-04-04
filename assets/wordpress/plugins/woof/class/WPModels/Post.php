<?php

namespace Woof\WPModels;

class Post
{

    // wordpress public properties
    public $ID;
    public $post_author;
    public $post_date;
    public $post_date_gmt;
    public $post_content;
    public $post_title;
    public $post_excerpt;
    public $post_status;
    public $comment_status;
    public $ping_status;
    public $post_password;
    public $post_name;
    public $to_ping;
    public $pinged;
    public $post_modified;
    public $post_modified_gmt;
    public $post_content_filtered;
    public $post_parent;
    public $guid;
    public $menu_order;
    public $post_type;
    public $post_mime_type;
    public $comment_count;
    public $filter;


    protected $author;
    private $categories = null;

    private $wordpressPost;


    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->post_title = $title;
        return $this;
    }


    public function getId()
    {
        return $this->ID;
    }

    public function getTitle()
    {
        return $this->post_title;
    }

    public function getExcerpt()
    {
        return $this->post_excerpt;
    }

    public function getContent()
    {
        return $this->post_content;
    }

    public function getDate()
    {
        return $this->post_date;
    }

    public function getThumbnailURL($size = 'post-thumbnail')
    {
        return get_the_post_thumbnail_url($this->getId(), $size);
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        if($this->author === null) {
            $this->author = User::getById($this->post_author);
        }
        return $this->author;
    }

    /**
     * @return Category[]
     */
    public function getCategories()
    {
        if($this->categories === null) {
            $categories = get_the_category($this->getId());
            $this->categories = [];
            foreach($categories as $category) {
                $this->categories[] = Category::getFromWordpressTerm($category);
            }
        }
        return $this->categories;
    }


    /**
     * @return $this
     */
    public function save()
    {
        $data = [
            'ID' => 0,
            'post_author' => $this->post_author,
            'post_date' => $this->post_date,
            'post_date_gmt' => $this->post_date_gmt,
            'post_content' => $this->post_content,
            'post_title' => $this->post_title,
            'post_excerpt' => $this->post_excerpt,
            'post_status' => $this->post_status,
            'comment_status' => $this->comment_status,
            'ping_status' => $this->ping_status,
            'post_password' => $this->post_password,
            'post_name' => $this->post_name,
            'to_ping' => $this->to_ping,
            'pinged' => $this->pinged,
            'post_modified' => $this->post_modified,
            'post_modified_gmt' => $this->post_modified_gmt,
            'post_content_filtered' => $this->post_content_filtered,
            'post_parent' => $this->post_parent,
            'guid' => $this->guid,
            'menu_order' => $this->menu_order,
            'post_type' => $this->post_type,
            'post_mime_type' => $this->post_mime_type,
            'comment_count' => $this->comment_count,
        ];
        if($this->getID()) {
            $data['ID'] = $this->getID();
            wp_insert_post($data);
        }
        else {
            $id = wp_insert_post($data);
            $this->loadById($id);
        }
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function loadById($id)
    {
        $post = get_post($id);
        $this->loadFromWordpressPost($post);
        return $this;
    }


    // ==========================================================================
    public function getMetadata($name, $single = true)
    {
        return get_post_meta(
            $this->getId(),
            $name,
            $single
        );
    }

    // ==========================================================================


    /**
     * @return \WP_Post
     */
    public function getWordpressPost()
    {
        return $this->wordpressPost;
    }

    /**
     * @param \WP_Post $post
     * @return $this
     */
    public function loadFromWordpressPost(\WP_Post $post)
    {
        $this->wordpressPost = $post;
        foreach($post as $attribute => $value) {
            $this->$attribute = $value;
        }
        return $this;
    }

    /**
     * @param \WP_Post $wpPost
     * @return static
     */
    public static function getFromWordpressPost(\WP_Post $wpPost)
    {
        $post = new static();
        $post->loadFromWordpressPost($wpPost);
        return $post;
    }

    /**
     * @param string $type
     * @param string $status
     * @param integer $count
     * @param string $orderBy
     * @param string $order
     * @return static[]
     */
    static public function getByType($type, $status='publish', $count = -1, $orderBy = 'date', $order = 'DESC')
    {
        $queryFilters = [
            'post_type' => $type,
            'post_status' => $status,
            'orderby' => $orderBy,
            'order' => $order,
            'posts_per_page' => $count //
        ];

        $query = new \WP_Query($queryFilters);

        $posts = [];
        foreach($query->posts as $wpPost) {
            $post = static::getFromWordpressPost($wpPost);
            $posts[] = $post;
        }

        return $posts;
    }
}
