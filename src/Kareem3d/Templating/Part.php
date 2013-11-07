<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\View;

class Part {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected static $sharedArgs = array();

    /**
     * @var array
     */
    protected $arguments = array();

    /**
     * @param $name
     */
    public function __construct($name)
    {
       $this->name = $this->realName($name);
    }

    /**
     * @param $args
     */
    public static function share(array $args)
    {
        static::$sharedArgs = array_merge(static::$sharedArgs, $args);
    }

    /**
     * @param $string
     * @param string $separator
     * @return array
     */
    public static function separatorFactory( $string, $separator = ',' ) {

        $partsNames = explode($separator, $string);

        $parts = array();

        foreach($partsNames as $partName)
        {
            if($partName) $parts[] = new static($partName);
        }

        return $parts;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return string
     */
    protected function realName( $name )
    {
        return strtolower(trim($name));
    }

    /**
     * @return mixed
     */
    public function printMe()
    {
        return $this->getView()->__toString();
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return array_merge(static::$sharedArgs, $this->arguments);
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return View::make($this->getViewName(), $this->getArguments());
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return 'parts.' . $this->name;
    }

}