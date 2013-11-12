<?php namespace Kareem3d\Templating;

class PartRepository {

    /**
     * @var Part[]
     */
    protected static $parts = array();

    /**
     * @param array $parts
     */
    public static function put(array $parts = array())
    {
        foreach($parts as $part) static::add($part);
    }

    /**
     * @param Part $part
     */
    public static function add(Part $part)
    {
        array_push(static::$parts, $part);
    }

    /**
     * @param array $parts
     */
    public static function set(array $parts = array())
    {
        static::$parts = $parts;
    }

    /**
     * @return \Kareem3d\Templating\Part[]
     */
    public static function get()
    {
        return static::$parts;
    }

    /**
     * @param $identifier
     * @return \Kareem3d\Templating\Part
     */
    public static function find($identifier)
    {
        foreach(static::get() as $part)
        {
            if($part->check($identifier))
            {
                return $part;
            }
        }
    }

    /**
     * @param string $identifier
     * @param $args
     */
    public static function share($identifier, $args)
    {
        if($part = static::find($identifier))
        {
            $part->share($args);
        }
    }
}