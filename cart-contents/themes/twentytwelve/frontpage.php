<html>
    <head>
        <title>Home :: <?=$store->name?></title>
    </head>
    <body>
        <h1><?=$store->name?></h1>
        <h2>Home</h2>
        <p>Welcome to the store!</p>
        
        <? if (isset($specials) && $specials->hasEntities()):
            echo '<h3>Specials</h3>';
            while ($specials->theEntity() != null): ?>
            
                <h4><a href="<?=$specials->theUrl()?>"><?=$specials->theTitle()?></a></h4>
            
        <?  endwhile; endif; ?>
        
        <? if (isset($promoted) && $promoted->hasEntities()):
            echo '<h3>Promotions</h3>';
            while ($promoted->hasEntities()): ?>
            
                <h4><a href="<?=$promoted->theUrl()?>"><?=$promoted->theTitle()?></a></h4>
            
        <?  endwhile; endif; ?>
        
    </body>
</html>