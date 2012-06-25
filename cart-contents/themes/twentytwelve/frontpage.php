<html>
    <head>
        <title>Home :: <?=$store->name?></title>
    </head>
    <body>
        <h1><?=$store->name?></h1>
        <h2>Home</h2>
        <p>Welcome to the store!</p>
        //get only the products for a specific category
        <?php query_products('category_name=tshirts'); ?>
        // Look if that category has specials, while it has specials, get the data of the products that have specials 
        <? if (has_specials ()): while (has_specials()): the_product?>

            echo '<h3>Specials</h3>';
            //$specials would have to be defined in the "specials plugin or module" where an actual All Specials page is created
                 <h4><a href="<?=$specials->theUrl()?>"><?=$specials->theTitle()?></a></h4>
             <h3><? product_name?></h3> 
                            <div><? product_image('thumbnail') ?>  </div> 
                            <p><? product_price() ?> </p> 
                            <button><? cart_button('buy-now' , 'learn-more');?> 
        <?  endwhile; endif; ?>
        
        // get ALL products
                <?php query_products(); ?>
                
                           <h3>Featured Products</h3>
//if is set as featured product, while it is set as featured product, get the data of the products that are featured
        <? if (is_featured ()): while (is_featured()): the_product?>
                            <h3><? product_name?></h3> 
                            <div><? product_image('thumbnail') ?>  </div> 
                            <p><? product_price() ?> </p> 
                            //buy now button function would allow you to set which type of button you want to display. 
                            //Learn more would bring you to the product detail page. Buy now would add to cart. 
                            <button><? cart_button('buy-now' , 'learn-more');?> 
                    <?  endwhile; endif; ?>
    </body>
</html>