
<table border="0" width="90%" align="center">

	<? foreach ($history->result() as $h) { ?>
	
		<tr>
			<td class="redLink"><?= opmDateTime($h->timestamp); ?></td>
			<td><?= $h->event ?></td>
		</tr>
		
	<? } ?>
	
</table>

