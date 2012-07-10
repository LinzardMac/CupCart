<?php

class Controller_Listing extends Controller
{
    public function get_index()
    {
        //  primary filter is a taxonomy for organizing what we're looking at
        $taxonomyArray = Hooks::applyFilter("listing_taxonomy", explode(":", $this->request->path[1]));
        
        //  see if the first element is a taxonomy
        $taxonomy = Taxonomy::getFromCacheByName(arr::get($taxonomyArray, 0, ''));
        if ($taxonomy == null)
        {
            $taxonomy = Taxonomy::getFromCacheByName("Category");
        }
        else
        {
            //  shift the taxonomy name off the array
            array_shift($taxonomyArray);
        }
        if ($taxonomy == null)
            throw new HTTP_Exception_404();
        
        $taxonomyTerm = TaxonomyTerm::get(arr::get($taxonomyArray, 0), $taxonomy);
        if ($taxonomyTerm == null)
            throw new HTTP_Exception_404();
        
        if (sizeof($taxonomyArray) > 1)
        {
            array_shift($taxonomyArray);
            while (sizeof($taxonomyArray) > 0)
            {
                $taxonomyTerm = Entity::getByMeta(array('parent','name'), array($taxonomyTerm->guid, array_shift($taxonomyArray)),
                    1, 0, 'TaxonomyTerm');
                if (sizeof($taxonomyTerm) < 1)
                    throw new HTTP_Exception_404();
                $taxonomyTerm = $taxonomyTerm[0];
            }
        }
        
        $metaKeys = array('belongsToTaxonomies');
        $metaValues = array($taxonomyTerm->guid);
        foreach($taxonomyTerm->getChildren(true) as $tmpTaxonomyTerm)
        {
            $metaKeys[] = 'belongsToTaxonomies';
            $metaValues[] = $tmpTaxonomyTerm->guid;
        }
        
        $count = Entity::getCountByMeta($metaKeys, $metaValues, 'Product');
        
        $perPage = Hooks::applyFilter("listing_results_per_page", 20);
        $currentPage = Hooks::applyFilter("listing_page_number", arr::get($_GET,'page',1));
        if (!is_numeric($currentPage)) $currentPage = 1;
        
        $offset = Hooks::applyFilter("listing_offset", ($currentPage - 1) * $perPage);
        
        $products = Entity::getByMeta($metaKeys, $metaValues, $perPage, $offset, 'Product');
        TPL::setTaxonomyTerm($taxonomyTerm);
        TPL::addProductLoop(new Loop($products));
        
        //  resolve the view to use
        $view = '';
        if (View::exists('listing-'.strtolower($taxonomy->name).'-'.strtolower($taxonomyTerm->name)))
            $view = 'listing-'.strtolower($taxonomy->name).'-'.strtolower($taxonomyTerm->name);
        else if (View::exists('listing-'.strtolower($taxonomy->name)))
            $view = 'listing-'.strtolower($taxonomy->name);
        else
        {
            $view = View::get()->name;
        }
        
        $view = Hooks::applyFilter("listing_".strtolower($taxonomy->name)."_".strtolower($taxonomyTerm->name)."_view", $view);
        
        View::get($view)->render();
    }
}