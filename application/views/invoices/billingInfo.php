<h3 class="userField">Verify Billing Info</h3>

<!--<textarea name="address" class="userField"><?= (isset($u->address) ? $u->address : null) ?></textarea>-->

<?= $user->staddress ?><br />
<?= ($user->staddress2 ? $user->staddress2 . "<br />" : null) ?>
<?= $user->city ?>, <?= $user->state ?> <?= $user->zip ?><br />

<br /><br />


<h3 class="userField">Invoice Image</h3>

<? if (isset($invoice) && $invoice->invoiceimage_path) { ?>
							
	<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/<?= $invoice->id ?>" alt="" />
	<input type="hidden" name="invoice_imagepath" value="<?= $user->invoiceimage_path ?>" />
	
<? } else if ($user->invoiceimage_path) { ?>

	<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/0/<?= $user->userid ?>" alt="" />
	<input type="hidden" name="invoice_imagepath" value="<?= $user->invoiceimage_path ?>" />

<? } else { ?>

	Upload Image Below (jpg or gif, maximum 400x150 pixels).<br />
	<input type="file" name="invoiceImage" />
	<input type="hidden" name="invoice_imagepath" value="" />

<? } ?>