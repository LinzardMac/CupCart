<?php

class Controller_Page extends Controller
{
    public function get_index()
    {
        View::get()->render();
    }
}