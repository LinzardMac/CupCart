<?php

class Controller_Listing extends Controller
{
    public function get_index()
    {
        //  primary filter is a taxonomy for organizing what we're looking at
        $primaryFilter = Hooks::applyFilter("listing_primary_filter", $this->request->path[1]);
        
        //  primary filter is most likely to be a category
        $primaryFilterTaxonomy = Taxonomy::get($primaryFilter, "Category");
        if ($primaryFilterTaxonomy == null)
        {
            //  it's not a category, search for other taxonomies
            $primaryFilterTaxonomy = Taxonomy::get($primaryFilter);
        }
        
        if ($primaryFilterTaxonomy == null)
            throw new HTTP_Exception_404();
        
        $taxonomyType = TaxonomyType::getFromCacheByGuid($primaryFilterTaxonomy->typeGuid);
        
        $perPage = Hooks::applyFilter("listing_results_per_page", 20);
        $currentPage = Hooks::applyFilter("listing_page_number", arr::get($_GET,'page',1));
        if (!is_numeric($currentPage)) $currentPage = 1;
        
        $offset = Hooks::applyFilter("listing_offset", ($currentPage - 1) * $perPage);
        
        $products = Entity::getByMeta('belongsToTaxonomies', $primaryFilterTaxonomy->guid, $perPage, $offset, 'Product');
        TPL::setTaxonomy($primaryFilterTaxonomy);
        TPL::addProductLoop(new Loop($products));
        
        //  resolve the view to use
        $view = '';
        if (View::exists('listing-'.strtolower($taxonomyType->name).'-'.strtolower($primaryFilterTaxonomy->name)))
            $view = 'listing-'.strtolower($taxonomyType->name).'-'.strtolower($primaryFilterTaxonomy->name);
        else if (View::exists('listing-'.strtolower($taxonomyType->name)))
            $view = 'listing-'.strtolower($taxonomyType->name);
        else
        {
            $view = View::get()->name;
        }
        
        $view = Hooks::applyFilter("listing_".strtolower($taxonomyType->name)."_".strtolower($primaryFilterTaxonomy->name)."_view", $view);
        
        View::get($view)->render();
    }
}