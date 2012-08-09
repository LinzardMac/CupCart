<?php

class RouteInfo
{
    public $controller;
    public $action;
    public $entity;
    public $format;
    public $params;
    
    public function __construct($controller, $action, $format, $entity = null, $params = array())
    {
	$this->controller = $controller;
	$this->action = $action;
	$this->entity = $entity;
	$this->format = $format;
	$this->params = $params;
    }
}