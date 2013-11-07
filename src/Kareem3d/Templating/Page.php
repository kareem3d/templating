<?php namespace Kareem3d\Templating;

class Page {

    /**
     * @var string
     */
    public $identifier;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Page
     */
    protected static $default;

    /**
     * @param $identifier
     * @param Template $template
     * @return \Kareem3d\Templating\Page
     */
    public function __construct($identifier, Template $template)
    {
        $this->identifier = $identifier;
        $this->template  = $template;
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function check($identifier)
    {
        return $this->identifier == strtolower(trim($identifier));
    }

    /**
     * @param $args
     * @return mixed
     */
    public function printMe(array $args = array())
    {
        return $this->template->printMe($args);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->identifier;
    }
}