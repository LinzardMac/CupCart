<!DOCTYPE html>
<html> 
    <head> 
	<meta charset="utf-8" />
	<title><?=TPL::getStoreName()?> / <?=$activePanel->pageTitle?> / <?=$activePage->pageTitle?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link href="<? TPL::theThemeUrl(); ?>bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="<? TPL::theThemeUrl(); ?>bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<? TPL::theThemeUrl(); ?>style.css" />
	
	<!--[if lt IE 9]>
	    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    </head> 

    <body>
    
	<div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
		<div class="container-fluid">
		    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		    </button>
		    <a class="brand" href="#"><?=TPL::getStoreName()?></a>
		    <ul class="nav">
			<? foreach(Menu::$menus as $menu): ?>
			    <li><a href="<?=Router::url('admin', array('category1' => $menu->slug))?>"><?=$menu->menuTitle?></a></li>
			<? endforeach; ?>
		      <!--
		      <li class="active"><a href="#">Dashboard</a></li>
		      <li><a href="#">Products</a></li>
		      <li><a href="#">Inventory</a></li>
		      <li><a href="#">Orders</a></li>
		      <li><a href="#">Logistics</a></li>
		      <li><a href="#">Site Settings</a></li>
		      <li><a href="#">Reports</a></li>
		      -->
		    </ul>
		    
		    <ul class="nav pull-right">
			<li class="dropdown">
			    <a class="dropdown-toggle" data-toggle="dropdown" href="#">J. Carruthers <b class="caret"></b></a>
			    <div class="dropdown-menu">
				<div class="modal-header">
				    <h4>John Carruthers</h4>
				    <h5>Manager</h5>
				</div>
				<div class="modal-body">
				    <div class="row">
					<div class="span1"><img src="img/cupcart.png" alt="avatar" width="55" /></div>
					<div class="span2">
					    <h5>thismy@email.com</h5>
					</div>
					<div class="span3">
					    <a href="#" class="link-modal" >My Account</a>
					    <a href="#" class="link-modal" >Account Preferences</a>
					</div>
				    </div>
				</div>
				<div class="modal-footer">
				    <a href="#" class="btn btn-primary pull-right">Log Out</a>
				</div>
			    </div>
			</li>
		    </ul>
		</div>
	    </div>
	</div>
	
	<div class="container-fluid">
	    <div class="row-fluid">
		<div class="span2">
		    <ul class="nav nav-tabs nav-stacked affix span2">
			<? foreach($activePanel->submenus as $menu): ?>
			    <? if ($menu->function == null): ?>
				<li><h5><?=$menu->menuTitle?></h5></li>
			    <? else: ?>
				<li><a href="#"><?=$menu->menuTitle?></a></li>
			    <? endif; ?>
			<? endforeach; ?>
		    </ul>
		</div>
		<div class="span10">
		
		    <div class="alert">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>Update available!</strong> A new version of CupCart is available. <a href="#">Update now</a>.
		    </div>
		
		    <h4><?=$activePage->pageTitle?></h4>
		    <ul class="breadcrumb">
			<li><a href="<?=Router::url('admin', array('category1'=>$activePanel->slug))?>"><?=$activePanel->menuTitle?></a> <span class="divider">/</span></li>
			<li class="active"><?=$activePage->menuTitle?></li>
		    </ul>