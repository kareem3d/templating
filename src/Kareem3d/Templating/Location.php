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
     * @param Part $part
     */
    public function addPart( Part $part )
    {
        $this->parts[] = $part;
    }

    /**
     * @param $_partName
     * @return \Kareem3d\Templating\Part
     */
    public function findPart( $_partName )
    {
        foreach($this->parts as $part)
        {
            if($part->check($_partName))

                return $part;
        }
    }

    /**
     * @param $type
     * @return string
     */
    public function printAssets( $type )
    {
        $string = '';

        foreach($this->parts as $part)
        {
            $string .= $part->printAssets( $type );
        }

        return $string;
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
     * @return array|Part[]
     */
    public function getParts()
    {
        return $this->parts;
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