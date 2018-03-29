
<select name="productlineid" onchange="updateThumbs(1);" id="productLineSelect">

	<option value="0">Select Product Line...</option>
    
    <option value="ALL">ALL</option>
    
<? foreach ($productLines->result() as $p) { ?>

	<option value="<?= $p->productlineid ?>"><?= $p->productline ?></option>

<? } ?>
    	

</select>