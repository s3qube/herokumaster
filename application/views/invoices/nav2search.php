<a href="<?= base_url(); ?>invoices/search">Search Invoices</a>
<? if (checkPerms('can_generate_invoice_reports')) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>invoices/generateReport">Invoice Reports</a><? } ?>
