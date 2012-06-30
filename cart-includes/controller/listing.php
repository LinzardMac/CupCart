<?php

class Controller_Listing extends Controller
{
    public function get_index()
    {
        //  primary filter is a taxonomy for organizing what we're looking at
        $primaryFilter = Hooks::applyFilter("listing_primary_filter", $this->request->path[1]);
        
        //  primary filter is most likely to be a category
        $primaryFilterTaxonomy = Taxonomy::get($primaryFilter, "Category");
        var_dump($primaryFilterTaxonomy);
        if ($primaryFilterTaxonomy == null)
        {
            //  it's not a category, search for other taxonomies
            
        }
        
        View::get()->render();
    }
}