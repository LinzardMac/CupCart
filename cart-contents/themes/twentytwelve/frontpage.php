<html>
    <head>
        <title>Home :: <?=TPL::getStoreName()?></title>
    </head>
    <body>
        <h1><?=TPL::getStoreName()?></h1>
        <h2>Home</h2>
        <p>Welcome to the store!</p>
        
        <? if (TPL::hasProducts('specials')):
            echo '<h3>Specials</h3>';
            while (TPL::hasProducts('specials')): TPL::theProduct(); ?>
            
                <h4><a href="<?=TPL::getTheUrl()?>"><?=TPL::getTheTitle()?></a></h4>
            
        <?  endwhile; endif; ?>
        
        <? if (TPL::hasProducts('promoted')):
            echo '<h3>Promoted</h3>';
            while (TPL::hasProducts('promoted')): TPL::theProduct(); ?>
            
                <h4><a href="<?=TPL::getTheUrl()?>"><?=TPL::getTheTitle()?></a></h4>
            
        <?  endwhile; endif; ?>
        
    </body>
</html>