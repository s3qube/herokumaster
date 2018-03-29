<table cellpadding="0" cellspacing="0" border="0" width="100%" height="30" id="contentNavTable">
	<tr>
		
		<? if (!isset($contentNavOmitTitle)) { ?>
		
			<td class="searchProductNav">&nbsp;&nbsp;Showing Assets <?= $prodStart ?> - <?= $prodEnd ?> of <?= $totalAssets ?></td>
		
		<? } else {?>
		
			<td class="searchProductNav">&nbsp;&nbsp;Assets <?= $prodStart ?> - <?= $prodEnd ?> of <?= $totalAssets ?></td>
		
		<? } ?>
		
		
		<td class="searchProductNav" align="right"><?php echo $this->pagination->create_links(); ?></td>
		
		
	</tr>
</table>

