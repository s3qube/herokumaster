<table cellpadding="0" cellspacing="0" border="0" width="100%" height="30" id="contentNavTable">
	<tr>
		
		<? if (!isset($contentNavOmitTitle)) { ?>
		
			<td class="searchProductNav">&nbsp;&nbsp;Showing Products <?= $prodStart ?> - <?= $prodEnd ?> of <?= $totalProducts ?></td>
		
		<? } else {?>
		
			<td class="searchProductNav">&nbsp;&nbsp;Products <?= $prodStart ?> - <?= $prodEnd ?> of <?= $totalProducts ?></td>
		
		<? } ?>
		
		
		<td class="searchProductNav" align="right"><?php echo $this->pagination->create_links(); ?></td>
		
		<? if ((!isset($isQuickSearch)) && (checkPerms('can_sort_searches')) && ($showSort == true)) {  // sorting is not available for quick search! ?>
		
			<td class="searchProductNav" align="right" style="text-align:right;">
				
				<span style="color:#666666">Sorting:</span> &nbsp;
			
				<select name="orderBy" onChange="document.searchForm.submit();">
					
					<option value="id" <? if ($args['orderBy'] == 'id') echo "SELECTED" ?>>Product ID</option>
					<option value="productname" <? if ($args['orderBy'] == 'productname') echo "SELECTED" ?>>Product Name</option>
					<option value="propertyname" <? if ($args['orderBy'] == 'propertyname') echo "SELECTED" ?>>Property Name</option>
					<option value="category" <? if ($args['orderBy'] == 'category') echo "SELECTED" ?>>Category</option>
					<option value="appstatus" <? if ($args['orderBy'] == 'appstatus') echo "SELECTED" ?>>Approval Status</option>
					<option value="sampappstatus" <? if ($args['orderBy'] == 'sampappstatus') echo "SELECTED" ?>>Sample App Status</option>
					<option value="lastactivity" <? if ($args['orderBy'] == 'lastactivity') echo "SELECTED" ?>>Last Activity</option>
					<option value="createdate" <? if ($args['orderBy'] == 'createdate') echo "SELECTED" ?>>Date Created</option>
					<option value="numMasterFiles" <? if ($args['orderBy'] == 'numMasterFiles') echo "SELECTED" ?>># Master Files</option>
					<option value="numSeparations" <? if ($args['orderBy'] == 'numSeparations') echo "SELECTED" ?>># Separations</option>
				
				</select>
				
				
				
				<strong><?= buildAscDescUrl($args); ?></strong>
				
				&nbsp;
			
			</td>
		
		<? } ?>
		
	</tr>
</table>

