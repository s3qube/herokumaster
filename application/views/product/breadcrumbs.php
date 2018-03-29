<a href="<?=base_url()?>search/doSearch">Products</a>&nbsp;&nbsp;&nbsp;&gt;&nbsp;&nbsp;&nbsp;
<a href="<?=base_url()?>search/doSearch/<?=$product->propertyid?>"><?= $product->property ?></a>&nbsp;&nbsp;&nbsp;&gt;&nbsp;&nbsp;&nbsp;
<? if ($product->category) { ?><a href="<?= base_url(); ?>search/doSearch/<?= $product->propertyid ?>/0/<?= $product->categoryid ?>/"><?= $product->category ?></a>&nbsp;&nbsp;&nbsp;&gt;&nbsp;&nbsp;&nbsp;<? } ?>
<?= $product->productname ?>
