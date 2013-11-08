<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Kareem3d\AssetManager\AssetCollection;
use Kareem3d\AssetManager\Asset;

class XMLFactory {

    /**
     * @var \SimpleXMLElement
     */
    protected $pagesXml;

    /**
     * @var \SimpleXMLElement
     */
    protected $assetsXml;

    /**
     * @var array
     */
    protected $defaultAttributes = array();

    /**
     * @var XMLFactory
     */
    protected static $instance;

    /**
     * @param $pagesXmlFile
     * @param $assetsXmlFile
     * @return \Kareem3d\Templating\XMLFactory
     */
    private function __construct($pagesXmlFile, $assetsXmlFile)
    {
        $this->pagesXml = simplexml_load_file($pagesXmlFile);
        $this->assetsXml = simplexml_load_file($assetsXmlFile);
    }

    /**
     * @param $pagesXmlFile
     * @param $assetsXmlFile
     * @return XMLFactory
     */
    public static function instance( $pagesXmlFile = null, $assetsXmlFile = null )
    {
        if(! static::$instance)
        {
            static::$instance = new static($pagesXmlFile, $assetsXmlFile);
        }

        return static::$instance;
    }

    /**
     * @return Page[]
     */
    public function generatePages()
    {
        $pages = array();

        foreach($this->pagesXml->page as $page)
        {
            // Identifier is either the name of url
            $identifier = $this->string($page, 'name') ?: $this->string($page, 'url');

            $pages[] = new Page($identifier, $this->generateTemplate($page));
        }

        return $pages;
    }

    /**
     * @param $_identifier
     * @return \Kareem3d\Templating\Page
     */
    public function generatePage( $_identifier )
    {
        foreach($this->pagesXml->page as $page)
        {
            // Identifier is either the name of url
            $identifier = $this->string($page, 'name') ?: $this->string($page, 'url');

            if($_identifier == $identifier)
            {
                return new Page($identifier, $this->generateTemplate($page));
            }
        }
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getDefault()
    {
        if(property_exists($this->pagesXml, 'default'))
        {
            return $this->pagesXml->default;
        }
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        return property_exists($this->pagesXml, 'default');
    }

    /**
     * @return Location[]
     */
    public function getDefaultLocations()
    {
        if(isset($this->defaultAttributes['locations'])) return $this->defaultAttributes['locations'];

        return $this->defaultAttributes['locations'] = $this->generateLocations($this->getDefault());
    }

    /**
     * @return array
     */
    public function getDefaultTemplate()
    {
        if(isset($this->defaultAttributes['template'])) return $this->defaultAttributes['template'];

        return $this->defaultAttributes['template'] = $this->string($this->getDefault(), 'template');
    }

    /**
     * @param $page
     * @return Template
     */
    public function generateTemplate( $page )
    {
        $locations    = $this->generateLocations($page);
        $templateName = $this->string($page, 'template');

        // Merge default with the current configurations
        if($this->hasDefault())
        {
            $locations    = array_merge($this->getDefaultLocations(), $locations);
            $templateName = $templateName ?: $this->getDefaultTemplate();
        }

        // Return new template with either the pagesXml template or default template.
        return new Template($templateName, $locations, $this->generateAssetCollectionForTemplate( $templateName ));
    }

    /**
     * @param \SimpleXMLElement $element
     * @param $keys
     * @return array
     */
    protected function string(\SimpleXMLElement $element, $keys)
    {
        $values = array();

        $attributes = $element->attributes();

        foreach((array) $keys as $key)
        {
            $values[] = (string)$attributes[$key];
        }

        return count($values) == 1 ? $values[0] : $values;
    }

    /**
     * @param $page
     * @return Location[]
     */
    protected function generateLocations($page)
    {
        $locations = array();

        foreach($page->children() as $tagName => $value)
        {
            // Create new location
            $location = new Location($tagName);

            // Parts are separated by `|` in the pagesXml file
            $partsPieces = explode('|', $value);

            foreach($partsPieces as $partName)
            {
                $location->addPart(new Part($partName, $this->generateAssetCollectionForPart($partName)));
            }

            // Using tag name as key to prevent duplication...
            $locations[$tagName] = $location;
        }

        return $locations;
    }

    /**
     * @param $partName
     * @return \SimpleXMLElement
     */
    protected function generateAssetCollectionForPart($partName)
    {
        foreach($this->assetsXml->assetCollection as $assetCollection)
        {
            if($this->string($assetCollection, 'part') == $partName)
            {
                return new AssetCollection($this->generateAssets( $assetCollection ));
            }
        }
    }

    /**
     * @param $templateName
     * @return \SimpleXMLElement
     */
    protected function generateAssetCollectionForTemplate($templateName)
    {
        foreach($this->assetsXml->assetCollection as $assetCollection)
        {
            if($this->string($assetCollection, 'template') == $templateName)
            {
                return new AssetCollection($this->generateAssets( $assetCollection ));
            }
        }
    }

    /**
     * @param $assetCollection
     * @return Asset[]
     */
    protected function generateAssets(\SimpleXMLElement $assetCollection)
    {
        $assets = array();

        foreach($assetCollection->children() as $tagName => $assetPath)
        {
            $assets[] = new Asset(trim($assetPath), strtolower(trim($tagName)));
        }

        return $assets;
    }
}