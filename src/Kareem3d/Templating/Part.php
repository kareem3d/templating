<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Kareem3d\AssetManager\AssetCollection;

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
     * @var AssetCollection
     */
    protected $assetCollection;

    /**
     * @param $name
     * @param \Kareem3d\AssetManager\AssetCollection $assetCollection
     * @return \Kareem3d\Templating\Part
     */
    public function __construct($name, AssetCollection $assetCollection = null)
    {
        $this->name = $this->realName($name);
        $this->assetCollection = $assetCollection;
    }

    /**
     * @return AssetCollection
     */
    public function getAssetCollection()
    {
        return $this->assetCollection;
    }

    /**
     * @param $name
     * @return bool
     */
    public function check($name)
    {
        return $this->name === $this->realName($name);
    }

    /**
     * @param $type
     * @return string
     */
    public function printAssets( $type )
    {
        if($assetCollection = $this->assetCollection)
        {
            return $assetCollection->printType($type);
        }
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