
<h3 class="invoicePopLabel">Select Product:</h3>


<select name="opm_productid" class="invoicePopField" onchange="getImage(this.value);">

	<option value="0">Please Select...</option>

	<? foreach ($products->result() as $p) { ?>
	
		<option value="<?= $p->opm_productid ?>"><?= $p->productname ?> - <?= $p->category ?></option>
	
	<? } ?>

</select>