

<? foreach ($tosList->result() as $tos) { ?>
	<table border="0" class="searchProductTable">
		<tr>
			<td valign="middle">
				<span class="searchProductName"><a href="<?=base_url();?>tos/edit/<?=$tos->id?>"><?= $tos->tosname ?> </a></span>- effective <?= opmDate($tos->effectivedate); ?><br />
			</td>
		</tr>
	</table>
	<div class="searchDiv"></div>
<? } ?>

