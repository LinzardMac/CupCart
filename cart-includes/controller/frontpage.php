<?php

/**
 * Controller to handle the front page.
*/
class Controller_FrontPage extends Controller
{
    public function get_index()
    {
        //  here you would get current specials, any products that are being promoted etc.
        //  based on configuration by admin
        //  all of these should be loops
        $specials = new Loop(Entity::getByMeta('isSpecial', true, 5, 0, array('PhysicalProduct')));
        $promoted = new Loop(Entity::getByMeta('isPromoted', true, 5, 0, array('PhysicalProduct')));
        
        View::get()->set(array(
            'specials'  => $specials,
            'promoted'  => $promoted
        ))->render();
    }
}