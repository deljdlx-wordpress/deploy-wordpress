<?php
namespace Woof\Routing;


class Route
{
    protected $method;
    protected $pattern;
    protected $callback;
    protected $wordpressRegexp;
    protected $name;

    public function __construct($method, $patternWorpresss, $pattern, $callback, $name = null)
    {
        $this->method = $method;
        $this->wordpressRegexp = $patternWorpresss;
        $this->pattern = $pattern;
        $this->callback = $callback;
        $this->name = $name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getWordpressRegexp()
    {
        return $this->wordpressRegexp;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getName()
    {
        return $this->name;
    }

}
