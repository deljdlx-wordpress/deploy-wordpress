<?php


namespace Woof\WPModels;


class User
{

    public $ID;

    public $data = null;
    /*stdClass
        'ID' => null,
        'user_login' => null,
        'user_pass' => null,
        'user_nicename' => null,
        'user_email' => null,
        'user_url' => null,
        'user_registered' => null,
        'user_activation_key' => null,
        'user_status' => null,
        'display_name' => null
    */

    public $caps = [];
    public $roles = [];
    public $allcaps = [];
    public $filter = null;

    private $wordpressUser;


    public function getId()
    {
        return $this->ID;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->data->user_login;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->data->display_name;
    }

    /**
     * Alias of getDisplayName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->getDisplayName();
    }


    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->data->user_email;
    }


    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $role
     * @return boolean
     */
    public function is($role)
    {
        return static::hasRole($this, $role);
    }

    /**
     * @return \Wp_User
     */
    public function getWordpressUser()
    {
        return $this->wordpressUser;
    }

    /**
     *
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        $this->getWordpressUser()->add_role($role);
        return $this;
    }


    /**
     * check if a user has a role
     * @param \WP_User $user
     * @param  string $role
     * @return boolean
     */
    static public function hasRole($user, $role)
    {
        if(in_array($role, $user->roles)) {
            return true;
        }
        else {
            return false;
        }
    }


    // ==========================================================================
    public function getMetadata($name, $single = true)
    {
        return get_user_meta(
            $this->getId(),
            $name,
            $single
        );
    }



    // ==========================================================================


    /**
     * @param \WP_User $wpUser
     * @return User
     */
    public function loadFromWordpressUser(\WP_User $wpUser)
    {
        $this->wordpressUser = $wpUser;

        foreach($wpUser as $attribute => $value) {
            $this->$attribute = $value;
        }
        return $this;
    }

    /**
     * @param \WP_User $wpUser
     * @return User
     */
    public static function getFromWordpressUser(\WP_User $wpUser)
    {
        $user = new static();
        $user->loadFromWordpressUser($wpUser);
        return $user;
    }



    /**
     * check if user is connected
     * @return boolean
     */
    static public function isConnected()
    {
        $user = static::getCurrent();
        if($user->ID) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * get current user
     * @return \WP_User
     */
    public static function getCurrent()
    {
        return wp_get_current_user();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function loadById($id)
    {
        $wpUser = get_user_by('id', $id);
        $this->loadFromWordpressUser($wpUser);
        return $this;
    }

    /**
     * @param integer $id
     * @return User
     */
    public static function getById(int $id)
    {
        $wpUser = get_user_by('id', $id);
        return static::getFromWordpressUser($wpUser);
    }


    /**
     * Rerieve users by id list
     *
     * @param array $userIds
     * @return \Wp_User[]
     */
    static public function getByIds(array $userIds)
    {
        $wpUsers = get_users([
            'include' => $userIds
        ]);

        $users = [];
        foreach($wpUsers as $wpUser) {
            $users[] = static::getFromWordpressUser($wpUser);
        }
        return $users;
    }


    // DOC récupération liste  de users https://developer.wordpress.org/reference/functions/get_users/
    /**
     * Rerieve users by role
     *
     * @param string|array $role
     * @param string $orderBy
     * @param string $order
     * @return \Wp_User[]
     */
    static public function getByRole($role, $orderBy = 'user_nicename', $order = 'ASC')
    {
        if(is_string($role)) {
            $args = array(
                'role'    => $role,
                'orderby' => $orderBy,
                'order'   => $order
            );
        }
        elseif(is_array($role)) {
            $args = array(
                'role__in'    => $role,
                'orderby' => $orderBy,
                'order'   => $order
            );
        }
        else {
            throw new \Exception('$role parameter must be a string or an array. Passed value : ' . print_r($role, true));
        }
        $wpUsers =  get_users( $args );

        $users = [];
        foreach($wpUsers as $wpUser) {
            $wpUsers[] = static::getFromWordpressUser($wpUser);
        }

        return $users;
    }


}