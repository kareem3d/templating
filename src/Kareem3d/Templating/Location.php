<?php namespace Kareem3d\Templating;

class Location {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Part[]
     */
    protected $parts;

    /**
     * @param $name
     * @param array $parts
     */
    public function __construct($name, array $parts = array())
    {
        $this->name = $this->realName($name);
        $this->parts = $parts;
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
        return $this->name == $this->realName($name);
    }

    /**
     * @param string $separator
     * @return string
     */
    public function printParts( $separator = '' )
    {
        $string = '';

        foreach($this->parts as $part) {

            $string .= $part->printMe() . $separator;
        }

        return rtrim($string, $separator);
    }
}