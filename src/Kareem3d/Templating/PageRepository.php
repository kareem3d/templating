<?php namespace Kareem3d\Templating;

class PageRepository {

    /**
     * @var Page[]
     */
    protected static $pages = array();

    /**
     * @param array $pages
     */
    public static function put(array $pages = array())
    {
        foreach($pages as $page) static::add($page);
    }

    /**
     * @param Page $page
     */
    public static function add(Page $page)
    {
        array_push(static::$pages, $page);
    }

    /**
     * @param array $pages
     */
    public static function set(array $pages = array())
    {
        static::$pages = $pages;
    }

    /**
     * @return \Kareem3d\Templating\Page[]
     */
    public static function get()
    {
        return static::$pages;
    }

    /**
     * @param $name
     * @return Part
     */
    public static function findPart( $name )
    {
        foreach(static::get() as $page)
        {
            if($part = $page->findPart($name))
            {
                return $part;
            }
        }
    }

    /**
     * @param $identifier
     * @return \Kareem3d\Templating\Page
     */
    public static function find($identifier)
    {
        foreach(static::get() as $page)
        {
            if($page->check($identifier))
            {
                return $page;
            }
        }
    }

    /**
     * @param string $identifier
     * @param $args
     */
    public static function share($identifier, $args)
    {
        if($page = static::find($identifier))
        {
            $page->share($args);
        }
    }
}