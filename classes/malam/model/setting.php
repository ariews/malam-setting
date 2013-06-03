<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  arie
 */

class Malam_Model_Setting extends ORM
{
    /**
     * Table name
     * @var string
     */
    protected $_table_name          = 'settings';

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('max_length', array(':value', 100)),
                array(array($this, 'unique'), array('name', ':value')),
            ),
        );
    }

    /**
     * Filter definitions for validation
     *
     * @return array
     */
    public function filters()
    {
        return array(
            'value' => array(
                array(array($this, 'check_for_array_or_object')),
            )
        );
    }

    /**
     * Check for array or object, when found serialized it
     * Different with serialize column
     *
     * @param mix $value
     * @return string
     */
    protected function check_for_array_or_object($value)
    {
        if (is_array($value) || is_object($value))
        {
            $value = serialize($value);
        }

        return $value;
    }

    /**
     * Handles retrieval of all model values, relationships, and metadata.
     *
     * @param   string $column Column name
     * @return  mixed
     */
    public function __get($column)
    {
        $return = parent::__get($column);

        if ($column == 'value' && preg_match('!^(a|o):\d+:!i', $return))
        {
            $return = unserialize($return);
        }

        return $return;
    }

    public function find_by_name($name)
    {
        return $this->where('name', '=', $name)->find();
    }

    public function create_or_update($values, $overwrite = TRUE)
    {
        $check = $this->find_by_name($values['name']);

        /* @var $check Model_Setting */

        if (($check->loaded() && TRUE === $overwrite) || ! $check->loaded())
        {
            $check->values($values)->save();
        }

        return $check;
    }

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param  array $values   Array of column => val
     * @param  array $expected Array of keys to take from $values
     * @return ORM
     */
    public function values(array $values, array $expected = NULL)
    {
        if (NULL === $expected || empty($expected))
        {
            $expected = array('name', 'value');
        }

        return parent::values($values, $expected);
    }
}