<?php namespace Kareem3d\Templating;

use Illuminate\Support\Facades\View;

class Template {

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
     */
    public function __construct($name, $locations = array())
    {
        $this->locations = $locations;
        $this->name      = $this->realName($name);
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
     * @param array $args
     * @return mixed
     */
    public function printMe(array $args = array())
    {
        // Share arguments for parts
        Part::share($args);

        return $this->getView($args)->__toString();
    }

    /**
     * @param array $args
     * @return mixed
     */
    public function getView(array $args = array())
    {
        $args['template'] = $this;

        return View::make($this->getViewName(), $args);
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

            return $location->printParts( $separator );
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