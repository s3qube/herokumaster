<select name="productLineIDs[]" class="userField" MULTIPLE>

	<? if ($numProductLines > 0) { ?>

	
		<? foreach ($productLines->result() as $pl) { ?>
		
			<option value="<?= $pl->productlineid ?>" <? if ($pl->isassigned) echo "SELECTED"?>><?= $pl->productline ?></option>
		
		<? } ?>
	
	<? }  else { ?>
	
			<option value="0">No Product Lines</option>
	
	<? } ?>

</select>

<br><br>
