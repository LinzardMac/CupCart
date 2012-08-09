<?php

/**
 * Base controller class.
 * @abstract
*/
abstract class Controller
{
    /**
     * @var Request Executing request information.
    */
    public $request;
    /**
     * @var RouteInfo Matched route.
    */
    public $routeInfo;
}