<!-- <pre>
<? //print_r($c); ?>
</pre> -->
<? if ($c->ishourly && $user->ishourly) { ?>

	<input type="hidden" name="chargeamount" value="0" />

	<h3 class="invoicePopLabel">Hours:</h3>

	<input type="text" name="hours" class="invoicePopField" style="width:200px;" value="<? if (isset($c->hours)) echo $c->hours;?>" />
	
	<h3 class="invoicePopLabel">Hourly Rate:</h3>
	
	<?= $invoice->currencysymbol ?><input type="text" name="hourlyrate" value="<?= $user->hourlyrate ?>" class="invoicePopField" style="width:200px;" />

<? } elseif ($c->chargetype == 'Submission Fee') { ?>


	<h3 class="invoicePopLabel">Submission Fee:</h3>

	<?= $invoice->currencysymbol ?><input type="text" name="chargeamount" class="invoicePopField" value="<?= (isset($c->chargeamount) ? $c->chargeamount : (isset($user->submissionfee) ? $user->submissionfee : null) ) ?>" style="width:200px;" />
	

<? } else { ?>

	<input type="hidden" name="hours" value="0" />
	<input type="hidden" name="hourlyrate" value="0" />
	
	<? if (isset($c->chargetypeid) && $c->chargetypeid == $this->config->item('invCTOther'))  { ?>
	
		<h3 class="invoicePopLabel">Charge Description <small>(25 chars max)</small>:</h3>

		<input type="text" name="chargedescription" class="invoicePopField" style="width:200px;" value="<?= (isset($c->chargedescripton) ? $c->chargedescription : null) ?>" />
	
	<? } else { ?>
	
		<input type="hidden" name="chargedescription" class="invoicePopField" value="" />
	
	<? } ?>

	<h3 class="invoicePopLabel">Charge Amount:</h3>

	<?= $invoice->currencysymbol ?><input type="text" name="chargeamount" class="invoicePopField" style="width:200px;" value="<? if (isset($c->chargeamount)) echo $c->chargeamount;?>" />

	
	

<? } ?>

<br /><br />
