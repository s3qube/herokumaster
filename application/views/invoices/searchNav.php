<table cellpadding="0" cellspacing="0" border="0" width="100%" height="30" id="contentNavTable">
	<tr>
		
		<td class="searchProductNav">&nbsp;&nbsp;Showing Invoices <?= $prodStart ?> - <?= $prodEnd ?> of <?= $totalInvoices ?></td>
		
			
		<td class="searchProductNav" align="right"><?php echo $this->pagination->create_links(); ?></td>
		
		<? if (checkPerms('can_sort_searches')) {  // sorting is not available for quick search! ?>
		
			<td class="searchProductNav" align="right" style="text-align:right;">
				
				<span style="color:#666666">Sorting:</span> &nbsp;

			
				<select name="orderBy" onChange="document.searchForm.submit();">
					
					<option value="id" <? if ($args['orderBy'] == 'id') echo "SELECTED" ?>>Invoice ID</option>
					<option value="username" <? if ($args['orderBy'] == 'productname') echo "SELECTED" ?>>Username</option>
					<option value="statusid" <? if ($args['orderBy'] == 'propertyname') echo "SELECTED" ?>>Status</option>
					<option value="total" <? if ($args['orderBy'] == 'category') echo "SELECTED" ?>>Total</option>
					<option value="createdate" <? if ($args['orderBy'] == 'appstatus') echo "SELECTED" ?>>Date Created</option>
					
				
				</select>
				
				
				
				<strong><?= buildAscDescUrl($args); ?></strong>
				
				&nbsp;
			
			</td>
		
		<? } ?>
		
	</tr>
</table>

