<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Kareem3d\AssetManager\AssetCollection;

class Part extends Viewable {

    /**
     * @var string
     */
    protected $name;

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
     * @return string
     */
    public function getViewName()
    {
        preg_match('#\((.*?)\)#', $this->name, $between_brackets);

        $name = empty($between_brackets) ? $this->name : str_replace($between_brackets[0], '', $this->name);

        return 'parts.' . $name;
    }
}