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
    private static $instance;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @return Setting
     */
    public static function instance()
    {
        empty(self::$instance) && self::$instance = new self();
        return self::$instance;
    }

    private function __construct()
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

    public function set($name, $value, $overwrite = TRUE)
    {
        $data = array(
            'name'      => $name,
            'value'     => $value,
        );

        if (! $this->is_exists($name) || $overwrite)
        {
            $this->Dco($name);
        }

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
            $c = $this->Gco($name);
            return $c['object']->value;
        }

        return $default;
    }

    public function remove($name)
    {
        if ($this->is_exists($name))
        {
            $c = $this->Gco($name);
            $c['object']->delete();
            return $this->Dco($name);
        }

        return FALSE;
    }

    public function is_exists($name)
    {
        $result = $this->Gco($name);

        if (NULL === $result)
        {
            $object = ORM::factory('setting')->find_by_name($name);
            $result = array('object' => $object, 'exists' => $object->loaded());
            $this->Sco($name, $result);
        }

        return $result['exists'];
    }

    /**
     * Get Cache Object
     *
     * @param string $name
     * @return array
     */
    protected function Gco($name)
    {
        return $this->cache->get("Setting:{$name}");
    }

    /**
     * Save Cache Object
     *
     * @param string $name
     * @param mix $value
     * @return boolean
     */
    protected function Sco($name, $value)
    {
        return $this->cache->set("Setting:{$name}", $value);
    }

    /**
     * Delete Cache Object
     *
     * @param string $name
     * @return boolean
     */
    protected function Dco($name)
    {
        return $this->cache->delete("Setting:{$name}");
    }
}