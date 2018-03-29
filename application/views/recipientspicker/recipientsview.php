<div id="recipientschooser">
	<form name="recipientsChooser" method="post" id="recipientsChooser" action="<?= base_url() ?>recipients/submit">
		<table>
		<? foreach ($recipients->result() as $r) { ?>
			<tr>
				<td><input type="checkbox"><?= $r->usergroup ?><input type="checkbox" name="<?= $r->usergroup?>"></td>
			</tr>
		<? } ?>		
		</table>
		<input type="submit" value="Submit">
	</form>
</div>