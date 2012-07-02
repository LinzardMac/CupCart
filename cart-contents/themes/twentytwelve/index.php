<? TPL::theHeader(); ?>

<div id="main">
    <? if (TPL::hasProducts()): while (TPL::hasProducts()): ?>
        <h4><a href="<?=TPL::getTheProductUrl()?>"><?=TPL::getTheProductTitle()?></a></h4>
    <? endwhile; else: ?>
        <h2>No Products</h2>
        <p>No products were found, sorry.</p>
    <? endif; ?>
</div>

<? TPL::theFooter(); ?>