<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8" />
<title>Purchase / Hold</title>
<link rel="stylesheet" href="<?= base_url() ?>resources/opm_popup.css" />
<link rel="stylesheet" href="<?= base_url() ?>resources/datepicker.css" />
<script src="<?= base_url() ?>resources/js/datepicker.js"></script>
<script src="<?= base_url() ?>resources/js/mootools-release-1.11.js"></script>
<script src="<?= base_url() ?>resources/js/opm_scripts.js"></script>
</head>

<body style="background-color: #fff;">

<?php if($mode == 'redirect'): ?>

	<h1>Purchase saved successfully.</h1>

	<p><input type="button" onclick="window.parent.Shadowbox.close()" value="Okay" /></p>
	
<?php else: ?>

	<?php if($mode == 'error'): ?>
	
		<h1>Error</h1>
		
		<ul>
		<?php foreach($errors as $error): ?>
		
			<li><?php print $error; ?></li>
			
		<?php endforeach; ?>
		</ul>
		
	<?php endif; ?>
	
	<h1>Account: <?php print $account->account; ?></h1>

	<form method="post" action="/products/savePurchase">
	
	<input type="hidden" name="opm_productid" value="<?php print $opm_productid; ?>" />
	<input type="hidden" name="accountid" value="<?php print $accountid; ?>" />
	
	<p>
	<label for="purchasetypeid">This is a:</label>
	<select name="purchasetypeid">
		
		<?php foreach($purchaseTypes->result() as $pt): ?>
		
			<option value="<?php print $pt->id; ?>"><?php print $pt->purchasetype; ?></option>
		
		<?php endforeach; ?>
		
	</select>
	</p>

	<p>
	<label for="enddate">End date (mm-dd-yyyy):</label>
	<input type="text" class="date w8em format-m-d-y divider-dash highlight-days-12" id="dp-normal-b1" name="enddate" value="<?php print date('m-d-Y', strtotime('+' . $this->config->item('defaultPurchaseLength') . ' days')); ?>" maxlength="10" />
	</p>
	
	<p>
	<label for="isexclusive">Is exclusive?:</label>
	<input type="checkbox" name="isexclusive" />
	</p>

	<p><input type="submit" name="save" value="Save" /></p>

	</form>
	
<?php endif; ?>
				
</body>

</html>
