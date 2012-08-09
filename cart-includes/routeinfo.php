<?php

class RouteInfo
{
    public $controller;
    public $action;
    public $entity;
    public $format;
    public $params;
    public $route;
    
    public function __construct($route, $controller, $action, $format, $entity = null, $params = array())
    {
	$this->route = $route;
	$this->controller = $controller;
	$this->action = $action;
	$this->entity = $entity;
	$this->format = $format;
	$this->params = $params;
    }
}