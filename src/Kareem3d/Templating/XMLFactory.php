<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class XMLFactory {

    /**
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * @var array
     */
    protected $defaultAttributes = array();

    /**
     * @param $xmlFile
     */
    public function __construct($xmlFile)
    {
        $this->xml = simplexml_load_file($xmlFile);
    }

    /**
     * @return Page[]
     */
    public function generatePages()
    {
        $pages = array();

        foreach($this->xml->page as $page)
        {
            // Identifier is either the name of url
            $identifier = $this->string($page, 'name') ?: $this->string($page, 'url');

            $pages[] = new Page($identifier, $this->generateTemplate($page));
        }

        return $pages;
    }

    /**
     * @param $_pageName
     * @param array $args
     * @return mixed
     */
    public function get($_pageName, array $args = array())
    {
        $currentTemplate = $this->getCurrentTemplate($_pageName);

        return $currentTemplate ? $currentTemplate->printMe($args) : '';
    }

    /**
     * @param $_pageName
     * @return Template
     */
    public function getCurrentTemplate( $_pageName )
    {
        foreach($this->xml->page as $page)
        {
            $pageUrl  = $this->string($page, 'url');
            $pageName = $this->string($page, 'name');

            $urlMatch  = $pageUrl && Request::is(str_replace(URL::to(''), '',$pageUrl));
            $pageMatch = $pageName === $_pageName;

            // If either the url match or given page name match then generate the template from xml and return it.
            if($urlMatch or $pageMatch)
            {
                return $this->generateTemplate($page);
            }
        }
    }


    /**
     * @return \SimpleXMLElement
     */
    public function getDefault()
    {
        if(property_exists($this->xml, 'default'))
        {
            return $this->xml->default;
        }
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        return property_exists($this->xml, 'default');
    }

    /**
     * @return Location[]
     */
    public function getDefaultLocations()
    {
        if(isset($this->defaultAttributes['locations'])) return $this->defaultAttributes['locations'];

        return $this->defaultAttributes['locations'] = $this->getLocations($this->getDefault());
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
        $locations    = $this->getLocations($page);
        $templateName = $this->string($page, 'template');

        // Merge default with the current configurations
        if($this->hasDefault())
        {
            $locations    = array_merge($this->getDefaultLocations(), $locations);
            $templateName = $templateName ?: $this->getDefaultTemplate();
        }

        // Return new template with either the xml template or default template.
        return new Template($templateName, $locations);
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
    protected function getLocations($page)
    {
        $locations = array();

        foreach($page->children() as $tagName => $value)
        {
            // Using tag name as key to prevent duplication...
            $locations[$tagName] = new Location($tagName, Part::separatorFactory((string) $value, '|'));
        }

        return $locations;
    }

}