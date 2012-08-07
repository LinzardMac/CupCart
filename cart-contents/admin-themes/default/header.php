<html> 
<head> 
<!--<meta charset="utf-8">-->
    <title>My Dashboard :: <?=TPL::getStoreName()?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="<? TPL::theThemeUrl(); ?>bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<? TPL::theThemeUrl(); ?>bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<? TPL::theThemeUrl(); ?>bootstrap/css/bootstrap.css" rel="stylesheet">
  <!--  <link href="<? TPL::theThemeUrl(); ?>bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
<link rel="stylesheet" type="text/css" href="<? TPL::theThemeUrl(); ?>style.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="<? TPL::theThemeUrl(); ?>bootstrap/js/bootstrap.js" ></script> 
  <!-- <script src="<? TPL::theThemeUrl(); ?>bootstrap/js/bootstrap.min.js" type="javascript"></script> -->
<script type="text/javascript">

    $(".alert").alert()
    </script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Work',     11],
          ['Eat',      2],
          ['Commute',  2],
          ['Watch TV', 2],
          ['Sleep',    7]
        ]);

        var options = {
          title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

</head> 

<body> 

<div id="wrapper"> 
<div id="header">
YOUR LOGO HERE
<!--
<div class="alert fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Oh dear. It seems that you've gone and blown something up! 
          </div>
-->
</div>

<div id="sidebar">

    <? foreach(Menu::$menus as $menu): ?>
	<div>
	    <h2><?=$menu->menuTitle?></h2>
	    <ul>
		<? foreach($menu->submenus as $submenu): ?>
		    <li><?=$submenu->menuTitle?></li>
		<? endforeach; ?>
	    </ul>
	</div>
    <? endforeach; ?>

<!--
<div>
<h2> Heading</h2> 
<ul>
<li>Item 1 </li>
<li>Item 2</li>
</ul>
</div>
<div>
<h2> Heading</h2> 
<ul>
<li>Item 1 </li>
<li>Item 2</li>
</ul>
</div>
<div>
<h2> Heading</h2> 
<ul>
<li>Item 1 </li>
<li>Item 2</li>
</ul>
</div>
<div>
<h2> Heading</h2> 
<ul>
<li>Item 1 </li>
<li>Item 2</li>
</ul> 
</div>
-->
</div> 
<div id="main"> 
    <!--
    <ul class="nav nav-tabs">
        <li><a href="#dashboard" data-toggle="tab">Dashboard</a></li>
        <li><a href="#stats" data-toggle="tab">Stats</a></li>
        <li><a href="#settings" data-toggle="tab">Settings</a></li>
        <li><a href="#plugins" data-toggle="tab">Plugins</a></li>
        <li><a href="#themes" data-toggle="tab">Themes</a></li>
    </ul>
    -->
    <div id="main-content" class="tab-content">
        <div class="tab-pane active" id="dashboard">