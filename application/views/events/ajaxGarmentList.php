<table class="reqFormGarmentList">
		
	<? foreach ($garments as $key => $g) { ?>
	
		<tr>	
	
			<td><?= $g['category'] ?><input type="hidden" name="g_categoryid[<?= $key ?>]" value="<?= $g['categoryid'] ?>"  /></td>
			<td><?= $g['color'] ?><input type="hidden" name="g_colorid[<?= $key ?>]" value="<?= $g['colorid'] ?>"  /></td>
			<td><?= $g['qty'] ?><input type="hidden" name="g_qty[<?= $key ?>]" value="<?= $g['qty'] ?>"  /></td>
			<td><a href="#" style="color:red;" class="garmentListRemover" onclick="removeGListItem(<?= $key ?>); return false;">X</a></td>
		</tr>
	<? } ?>
	
</table>