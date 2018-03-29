<h3 class="invoicePopLabel">Verify Billing Info:</h3>
		
	<!--<textarea name="billinginfo"><?= $user->staddress ?></textarea>-->
	
<?= $user->staddress ?><br />
<?= ($user->staddress2 ? $user->staddress2 . "<br />" : null) ?>
<?= $user->city ?>, <?= $user->state ?> <?= $user->zip ?><br />
	
<h3 class="invoicePopLabel">Tax ID:</h3>

<?= $user->taxid ?>

<h3 class="invoicePopLabel">VAT #::</h3>

<?= $user->vatnumber ?>
	
<h3 class="invoicePopLabel">Invoice Image:</h3>

	<? if ($user->invoiceimage_path) { ?>
							
		<img src="<?= base_url(); ?>imageclass/viewInvoiceImage/0/<?= $user->userid ?>" alt="" />
		<input type="hidden" name="invoice_imagepath" value="<?= $user->invoiceimage_path ?>" />
	
	<? } else { ?>
	
		No image uploaded.
		<input type="hidden" name="invoice_imagepath" value="" />
	
	<? } ?>
	
	<!-- UPLOAD NEW IMAGE AREA -->
	<!--<br />
	Upload Image Below (jpg or gif, maximum 400x150 pixels).<br />
	<input type="file" name="invoiceImage" />-->