<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\View;
use Kareem3d\AssetManager\AssetCollection;

abstract class Viewable {

    /**
     * @var array
     */
    protected $arguments = array();

    /**
     * @var AssetCollection
     */
    protected $assetCollection;

    /**
     * @return AssetCollection
     */
    public function getAssetCollection()
    {
        return $this->assetCollection;
    }

    /**
     * @param AssetCollection $assetCollection
     */
    public function setAssetCollection( AssetCollection $assetCollection )
    {
        $this->assetCollection = $assetCollection;
    }

    /**
     * @param string $type
     * @return string
     */
    public abstract function printAssets( $type );

    /**
     * @param $args
     * @throws \Exception
     */
    public function share( $args )
    {
        if($args instanceof \Closure)
        {
            call_user_func_array($args, array($this));
        }

        elseif(is_array($args))
        {
            $this->addArguments($args);
        }

        else
        {
            throw new \Exception('Can\'t Share the given arguments. Only array and closures are allowed');
        }
    }

    /**
     * @param array $arguments
     */
    public function addArguments(array $arguments)
    {
        $this->arguments = array_merge($this->arguments, $arguments);
    }

    /**
     * @param $key
     * @param $value
     */
    public function addArgument($key, $value)
    {
        $this->arguments[ $key ] = $value;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getArgument( $key )
    {
        return $this->arguments[ $key ];
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * You can pass extra arguments and it will be shared..
     *
     * @param array $args
     * @return mixed
     */
    public function printMe(array $args = array())
    {
        $this->share($args);

        return $this->getView()->__toString();
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
    public abstract function getViewName();

    /**
     * @param $property
     * @return mixed
     */
    public function __get( $property )
    {
        return $this->getArgument( $property );
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set( $key, $value )
    {
        $this->addArgument($key, $value);
    }
}