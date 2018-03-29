

Hello,

The following has been updated and requires your attention:

<?= $product->property ?> - <?= $product->productname ?>, is available for viewing.

<? if ($comment) { ?>

The following comment was added by <?= $commentUsername ?>:
-----------------------------------------------------------

<?= $comment ?>



-----------------------------------------------------------

<? } ?>


<?= base_url(); ?>products/view/<?= $product->opm_productid ?>

Bravado OPM