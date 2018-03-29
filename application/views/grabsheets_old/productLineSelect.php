
<select name="productlineid" onchange="updateThumbs();" id="productLineSelect">

	<option value="0">Select Product Line...</option>
    	
<? foreach ($productLines->result() as $p) { ?>

	<option value="<?= $p->productlineid ?>"><?= $p->productline ?></option>

<? } ?>
    	

</select>