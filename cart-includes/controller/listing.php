<?php

class Controller_Listing extends Controller
{
    public function get_index()
    {
        View::get()->render();
    }
}