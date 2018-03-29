<table cellpadding="0" cellspacing="0" border="0" width="100%" height="30" id="contentNavTable">
	<tr>
		
		<td class="searchProductNav">&nbsp;&nbsp;Showing Accounts <?= $prodStart ?> - <?= $prodEnd ?> of <?= $totalAccounts ?></td>
		<td class="searchProductNav" align="right"><?php echo $this->pagination->create_links(); ?></td>
		
	</tr>
</table>