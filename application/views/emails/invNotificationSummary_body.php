The following invoices need your attention:

<? foreach ($invoices as $i) { ?>
<?= base_url(); ?>invoices/edit/<?=$i['invoiceid']?>

<? } ?>
