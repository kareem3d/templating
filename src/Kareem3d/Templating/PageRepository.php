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
     * @return array|Page[]
     */
    public static function get()
    {
        return static::$pages;
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

}