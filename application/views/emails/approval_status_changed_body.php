<?= $username ?> <?= $approvalstatus ?> <?= $productInfo->property ?> - <?= $productInfo->productname ?>


<? if (isset($revisions) && $revisions != '') { ?>

The Following Revisions Were Submitted:

<?= $revisions ?>

<? } ?>

<?= base_url(); ?>products/view/<?= $productInfo->opm_productid ?>