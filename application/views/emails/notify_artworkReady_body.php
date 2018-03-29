Artwork is ready for <?= $product->property ?> - <?= $product->productname ?>

<? if ($comment) { ?>

The following comment was added by <?= $commentUsername ?>:
-----------------------------------------------------------

<?= $comment ?>



-----------------------------------------------------------

<? } ?>

<?= base_url(); ?>products/view/<?= $product->opm_productid ?>