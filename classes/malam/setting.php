<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Malam_Setting
{
    /**
     * Setting
     * @var Setting
     */
    private static $instance;

    /**
     * Setting Table Name
     * @var sting
     */
    protected $table;

    public static function instance()
    {
        empty(self::$instance) && self::$instance = new self();
        return self::$instance;
    }

    private function __construct()
    {
        $this->table = Kohana::$config->load('setting.table');
    }

    public function __set($name, $value = NULL)
    {
        return $this->set($name, $value);
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

    public function set($name, $value, $overwrite = FALSE)
    {
        $is_exists = $this->is_exists($name);

        if (($is_exists && TRUE === $overwrite)
                OR
            (! $is_exists))
        {
            $value = (is_array($value) || is_object($value))
                ? serialize($value) : $value;

            $data = array(
                'name'  => $name,
                'value' => $value,
            );

            if ($is_exists)
            {
                DB::update($this->table)->set($data)
                    ->where('name', '=', $name)->execute();
            }

            else
            {
                DB::insert($this->table, array_keys($data))
                        ->values(array_values($data))
                        ->execute();
            }
        }

        return $this;
    }

    public function sets($data, $overwrite = FALSE)
    {
        foreach ($data as $name => $value)
        {
            $this->set($name, $value, $overwrite);
        }

        return $this;
    }

    public function get($name, $default = NULL, $as_object = FALSE)
    {
        if ($this->is_exists($name))
        {
            $s = DB::select()->from($this->table)->where('name', '=', $name)
                    ->execute(NULL, TRUE)->current();

            if (TRUE === $as_object)
                return $s;

            return (preg_match('!^(a|o):\d+:\{!i', $s->value))
                    ? unserialize($s->value) : $s->value;
        }

        return $default;
    }

    public function remove($name)
    {
        try
        {
            DB::delete($this->table)
                ->where('name', '=', $name)->execute();
        }
        catch (Exception $e)
        {
            return FALSE;
        }

        return TRUE;
    }

    public function is_exists($name)
    {
        return (bool) DB::select()->from($this->table)
                ->where('name', '=', $name)->execute()->count();
    }
}