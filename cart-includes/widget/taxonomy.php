<?php

/**
 * Widget for displaying a taxonomy listing.
*/
class Widget_Taxonomy extends Widget
{
	public function form($opts)
	{
		
	}
	
	public function display($args)
	{
		if (arr::get($args,'taxonomy','') == '')
			return;
		
		extract($args);
		
		$taxonomy = Taxonomy::getFromCacheByGuid($taxonomy);
		if ($taxonomy == null)
			return;
		
		echo $beforeWidget;
		
		if (arr::get($args,'title','') == '')
			$title = $taxonomy->name;
		echo $beforeTitle . $title . $afterTitle;
		
		Taxonomy::display(array('taxonomy' => $taxonomy->guid));
		
		echo $afterWidget;
	}
}