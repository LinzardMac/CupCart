<? TPL::theHeader(); ?>

<div id="main">
    <h2><? TPL::theTaxonomy(); ?>:</h2>
    
    <? if (TPL::hasProducts()): while(TPL::hasProducts()): TPL::theProduct(); ?>
        <h4><a href="<?=TPL::getTheProductUrl()?>"><?=TPL::getTheProductTitle()?></a></h4>
    <? endwhile;
       else: ?>
        <p>This page has no products, sorry.</p>
    <? endif; ?>
    
</div>

<? TPL::theFooter(); ?>