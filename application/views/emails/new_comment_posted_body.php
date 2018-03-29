<?= $username ?> posted the following comment to <?= $productInfo->property ?> - <?= $productInfo->productname ?>:


Subject: <?= $commentSubject ?>

Body: <?= $commentBody ?>

<?= base_url(); ?>products/view/<?= $productInfo->opm_productid ?>