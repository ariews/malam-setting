<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Malam_Setting
{
    /**
     * Setting
     *
     * @var Setting
     */
    protected static $instance;

    /**
     * @return Setting
     */
    public static function instance()
    {
        empty(self::$instance) && self::$instance = new Setting;
        return self::$instance;
    }

    public function __construct()
    {}

    public function __set($name, $value = NULL)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __unset($name)
    {
        return $this->remove($name);
    }

    public function __isset($name)
    {
        return $this->is_exists($name);
    }

    public function set($name, $value)
    {
        $data = array(
            'name'      => $name,
            'value'     => $value,
        );

        return ORM::factory('setting')->create_or_update($data, $overwrite);
    }

    public function sets($data, $overwrite = FALSE)
    {
        foreach ($data as $name => $value)
        {
            $this->set($name, $value, $overwrite);
        }

        return $this;
    }

    public function get($name, $default = NULL)
    {
        if ($this->is_exists($name))
        {
            return ORM::factory('setting')->find_by_name($name)->value;
        }

        return $default;
    }

    public function remove($name)
    {
        if ($this->is_exists($name))
        {
            return ORM::factory('setting')->find_by_name($name)->delete();
        }

        return FALSE;
    }

    public function is_exists($name)
    {
        return ORM::factory('setting')->find_by_name($name)->loaded();
    }
}
