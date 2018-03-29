<script language="javascript">

	$(document).ready(function() {
	
		$(".chzn-select").chosen(function() {
 			
 			
 
		});


	});

</script>

<div id="searcharea">

	<form name="searchForm" method="post" action="<?=base_url();?>search/submit">
		<input type="hidden" name="exportExcel" value="0" />
		
		<? if ($isWholesaleSearch) { ?>
		
			<input type="hidden" name="isWholesaleSearch" value="1" />
		
		<? } ?>
	
		<table border="0" cellpadding="3" width="100%">
			<tr>
				<td>Property :</td>
				<td>
					
					<select name="propertyid" class="searchField chzn-select">
			
						<option value="0" <? if ($args['propertyid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>" <? if ($args['propertyid'] == $p->propertyid) echo "SELECTED"; ?>><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
				
				<td>&nbsp;&nbsp;</td>
				
				<? if (isset($productLines)) { ?>
				
					<td>Product Line :</td>
					<td>

						<select name="productlineid" class="searchField chzn-select">
				
							<option value="0" <? if ($args['productlineid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($productLines->result() as $p) { ?>
								<option value="<?= $p->productlineid ?>" <? if ($args['productlineid'] == $p->productlineid) echo "SELECTED"; ?>><?= $p->productline ?></option>
							<? } ?>
						</select>

					</td>
				
				<? } ?>
				
			</tr>
			<tr>
				<td>Category :</td>
				<td>
				
					<select name="categoryid" class="searchField chzn-select">
			
						<option value="0" <? if ($args['categoryid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($categories->result() as $c) { ?>
						
							<option value="<?= $c->categoryid ?>" <? if ($args['categoryid'] == $c->categoryid) echo "SELECTED"; ?>><?= $c->category ?></option>
						
						<? } ?>
						
					</select>
				
				</td>
				
				<td></td>
				
				<? if (checkPerms('can_view_unapproved_products')) { ?>
				
				
					<td>Approval Status:</td>
					
					<td>
							
						<select name="approvalstatusid" class="searchField chzn-select">
				
							<option value="0" <? if ($args['approvalstatusid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
							
							<? foreach ($approvalStatuses as $key => $ap) { ?>
							
								<? if (!($ap['id'] == $this->config->item('appStatusExpired') && !checkPerms('can_view_expired_products'))) { ?>
									
									<option value="<?= $ap['id'] ?>" <? if (($args['approvalstatusid'] == $ap['id'])) echo "SELECTED"; ?>><?= $ap['status'] ?></option>

								<? } ?>

							<? } ?>
							
						</select>
					
					</td>
					
				
				<? } else { ?>
					
					<td></td><td></td>
					<input type="hidden" name="approvalstatusid" value="1" />
				
				<? } ?>
					
			</tr>
			
			<tr>
				<td>Product Name :</td>
				<td>
				
					<input name="searchtext" class="searchField" value="<?= ($args['searchtext'] ? $args['searchtext'] : null) ?>">
				
				</td>
				<td></td>
				
				<? if (checkPerms('search_by_usergroup')) { ?>
				
				<td>Usergroup:</td>
				
				<td>
						
					<select name="usergroupid" class="searchField chzn-select">
			
						<option value="0" <? if ($args['usergroupid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($usergroups as $key=>$ug) { ?>
								
							<option value="<?= $key ?>" <? if ($args['usergroupid'] == $key) echo "SELECTED"; ?>><?= $ug ?></option>
						
						<? } ?>
						
					</select>
				
				</td>
				
				<? } ?>
				
				
			</tr>
			
			<tr>
			
				<? if (checkPerms('search_by_designer')) { ?>
				
				<td>Designer:</td>
				
				<td>
						
					<select name="designerid" class="searchField chzn-select">
			
						<option value="0" <? if ($args['usergroupid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($designers->result() as $d) { ?>
								
							<option value="<?= $d->userid ?>" <? if ($args['designerid'] == $d->userid) echo "SELECTED"; ?>><?= $d->username ?></option>
						
						<? } ?>
						
					</select>
				
				</td>
				
				<td></td>
				
				<? } ?>
				
				
				
				<? if (checkPerms('search_by_productcode')) { ?>
				
				<td>Product Code:</td>
				
				<td>
						
					<input name="productcode" class="searchField" value="<?= ($args['productcode'] ? $args['productcode'] : null) ?>">
				
				</td>
				
				<? } ?>
				
				
			</tr>

			
			<tr>
			
				
				<td>Products Per Page:</td>
				
				<td>
						
					<input name="perPage" class="searchField" value="<?= ($args['perPage'] ? $args['perPage'] : $this->config->item('searchPerPage')) ?>">

				
				</td>
				
				<td></td>
				
				<? if (checkPerms('can_view_sample_approval')) { ?>
				
				<td>Sample Appvl:</td>
				
				<td>
						
					<select name="sampappstatusid" class="searchField chzn-select">
			
						<option value="0" <? if ($args['sampappstatusid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
						
						<? foreach ($approvalStatuses as $as) { ?>
						
							<option value="<?= $as['id'] ?>" <? if (($args['sampappstatusid'] == $as['id'])) echo "SELECTED"; ?>><?= $as['status'] ?></option>
						
						<? } ?>
						
					</select>
				
				</td>
				
				<? } ?>
				
			</tr>
			
			<tr>
			
				
			
				
				<? if (checkPerms('can_search_by_territory')) { ?>
				
				<td>Territories:</td>

				<td>
						
					<select name="territoryid" class="searchField chzn-select">
			
						<option value="0" <? if ($args['territoryid'] == '0') echo "SELECTED"; ?>>SHOW ALL</option>
						<option value="all" <? if ($args['territoryid'] == 'all') echo "SELECTED"; ?>>Worldwide</option>
						
						<? foreach ($territories as $t) { ?>
						
							<option value="<?= $t['territoryid'] ?>" <? if ($args['territoryid'] == $t['territoryid']) echo "SELECTED"; ?>><?= $t['territory'] ?></option>
						
						<? } ?>
						
					</select>
				
				</td>
				
				<? } else { ?>
				
					<td></td>
					<td></td>
				
				<? } ?>
				
				<td></td>
				
				<? if (checkPerms('can_search_by_masterfile_name')) { ?>
				
					<td>Master File Name:</td>           
					<td><input name="filename" class="searchField" value="<?= ($args['filename'] ? $args['filename'] : null) ?>" /></td>
				
				<? } ?>
				
			</tr>
			
			
			
				<tr>
				
					<? if (checkPerms('can_search_by_print_garment')) { ?>
					
						<td>Print/Garment Info:</td>
						
						<td>
								
							<input name="filmlocations" class="searchField" value="<?= ($args['filmlocations'] ? $args['filmlocations'] : null) ?>">
		
						
						</td>
						
						<td></td>
					
					<? } ?>
					
					<? if (checkPerms('search_by_creator')) { ?>
				
						<td>Created by:</td>
						
						<td>
								
							<select name="creatorid" class="searchField chzn-select">
					
								<option value="0" <? if ($args['creatorid'] == 0) echo "SELECTED"; ?>>SHOW ALL</option>
								
								<? foreach ($creators->result() as $c) { ?>
										
									<option value="<?= $c->userid ?>" <? if ($args['creatorid'] == $c->userid) echo "SELECTED"; ?>><?= $c->username ?> (<?= $c->usergroup ?>)</option>
								
								<? } ?>
								
							</select>
						
						</td>
					
					<? } ?>
					
		
				</tr>
				
				<tr>
				
					
						<td>OPM ID:</td>
						
						<td>
								
							<input name="opmid" class="searchField" />
		
						
						</td>
						
						<td></td>
					
					
				
					
		
				</tr>
			
			
			
				
			
				
				
	
			
			<tr>
				<td colspan="10" align="right"><br /><input type="submit" onclick="document.searchForm.exportExcel.value='0';" value="search"><? if (checkPerms('can_export_excel')) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" title="Use this button to download an excel spreadsheet of the items in the search." onclick="document.searchForm.exportExcel.value='1';document.searchForm.submit();return false;"><img src="<?= base_url(); ?>resources/images/excel.gif" alt="Export As Excel" width="22" height="22" border="0" align="absmiddle"/></a><? } ?></td>
			</tr>
		</table>

</div>