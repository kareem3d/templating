<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\View;
use Kareem3d\AssetManager\AssetCollection;

class Template extends Viewable {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Location[]
     */
    protected $locations;

    /**
     * @param $name
     * @param array $locations
     * @param \Kareem3d\AssetManager\AssetCollection $assetCollection
     * @return \Kareem3d\Templating\Template
     */
    public function __construct($name, $locations = array(), AssetCollection $assetCollection = null)
    {
        $this->locations = $locations;
        $this->name      = $this->realName($name);
        $this->assetCollection = $assetCollection;

        // Share template argument
        $this->addArgument('template', $this);
    }

    /**
     * @return array
     */
    public function getAssetCollections()
    {
        $assetCollections = array();

        if($assetCollection = $this->getAssetCollection())
        {
            $assetCollections[] = $assetCollection;
        }

        // Get locations parts
        foreach($this->getLocationsParts() as $parts)
        {
            foreach($parts as $part)
            {
                if($assetCollection = $part->getAssetCollection())
                {
                    $assetCollections[] = $assetCollection;
                }
            }
        }

        return $assetCollections;
    }

    /**
     * @param string $type
     * @return string
     */
    public function printAssets( $type )
    {
        return $this->printTemplateAssets($type) . $this->printLocationsAssets($type);
    }

    /**
     * @param $type
     * @return string
     */
    protected function printTemplateAssets($type)
    {
        if($assetCollection = $this->assetCollection)
        {
            return $this->assetCollection->printType( $type );
        }
    }

    /**
     * @param $type
     * @return string
     */
    protected function printLocationsAssets( $type )
    {
        $string = '';

        foreach($this->locations as $location)
        {
            $string .= $location->printAssets( $type );
        }

        return $string;
    }

    /**
     * @param $_partName
     * @return \Kareem3d\Templating\Part
     */
    public function findPart( $_partName )
    {
        foreach($this->locations as $location)
        {
            if($part = $location->findPart($_partName))
            {
                return $part;
            }
        }
    }

    /**
     * @return Part[]
     */
    public function getLocationsParts()
    {
        $parts = array();

        foreach($this->locations as $location)
        {
            $parts[] = $location->getParts();
        }

        return $parts;
    }

    /**
     * @param Location $location
     */
    public function addLocation(Location $location)
    {
        // If location doesn't exist
        if(! $this->findLocation($location->getName())) {

            $this->locations[] = $location;
        }
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
        return $this->name == $this->realName($name);
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return "templates.{$this->name}";
    }

    /**
     * @param $location
     * @param string $separator
     * @return string
     */
    public function printLocation( $location, $separator = '' )
    {
        if($location = $this->findLocation($location))
        {
            return $location->printParts( $separator, $this->getArguments() );
        }
    }

    /**
     * @param $locationName
     * @return Location|null
     */
    public function findLocation($locationName)
    {
        if($locationName instanceof Location) return $locationName;

        foreach($this->locations as $location) {

            if($location->check($locationName)) return $location;
        }
    }
}