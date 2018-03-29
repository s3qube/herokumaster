
<div id="searcharea">
		
	<form name="grabsheet" id="grabsheetForm" method="post" action="<?=base_url();?>grabsheets/save" onsubmit="return checkGrabsheetForm();">
	
	<? if (isset($grabsheet->grabsheetid) && $copy == false) { ?>
	
		<input type="hidden" name="grabsheetid" value="<?= $grabsheet->grabsheetid ?>" />
	
	<? } ?>
	
	<? if (isset($grabsheet->items)) {  // add hidden comment fields for edit mode. ?>
						
		<? foreach ($grabsheet->items as $i) { ?>
		
			<input type="hidden" id="gsItemComment_<?= $i->imageid ?>" name="gsItemComment_<?= $i->imageid ?>" value="<?= $i->comment ?>">
		
		<? } ?>
	
	<? } ?>
	
	<table border="0" cellpadding="3" width="95%">
			<tr>
				<td>Property :</td>
				<td>
					
					<select name="propertyid" id="propertyid" onChange="changeSelect();" class="searchField">
						
						<option value="0">Select Property</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
				
				<td>&nbsp;&nbsp;</td>	
				
				<td>Group :</td>
				<td>
					
					<select name="grabsheetgroupid" class="searchField">
						
						<option value="0">Select Group</option>
						
						<? foreach ($grabsheetGroups->result() as $g) { ?>
						
							<option value="<?= $g->grabsheetgroupid ?>" <? if ($grabsheet->grabsheetgroupid == $g->grabsheetgroupid) echo "SELECTED"; ?>><?= $g->grabsheetgroup ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
				
				
			</tr>
			
			<tr>
			
				<td>Product Line :</td>
				<td>
					
						<div id="sDiv">
		
							<select name="productlineid" id="productLineSelect" class="searchField">
								
								<option value="ALL">---</option>
							
							</select>
							
						</div>

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Title :</td>
				<td>
					
					<input type="text" name="title" class="searchField" value="<?= (isset($grabsheet->grabsheettitle)) ? $grabsheet->grabsheettitle : null ?><?= ($copy) ? " (copy)" : null ?>">
					
				</td>
	
			</tr>
			<tr>
			
				<td>OPM Product ID :</td>
				<td>
					
						<input type="text" class="searchField" id="opmproductid" />

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td></td>
				<td>
					
					
				</td>
	
			</tr>
			<tr>
			
				<td>Product Code :</td>
				<td>
					
						<input type="text" class="searchField" id="productcode" />

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td></td>
				<td>
					
					
				</td>
	
			</tr>
			
			<tr>
			
				<td>Designer :</td>
				<td>
					
					<select name="designerid" id="designerid" class="searchField">
						
						<option value="0">Select Designer</option>
						
						<? foreach ($designers->result() as $d) { ?>
						
							<option value="<?= $d->userid ?>"><?= $d->username ?></option>
						
						<? } ?>
						
					</select>

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td></td>
				<td>
					
					
				</td>
	
			</tr>
			
			<tr>
			
				<td>Category :</td>
				<td>
					
					<select name="categoryid" id="categoryid" class="searchField">
						
						<option value="0">Select Category</option>
						
						<? foreach ($categories->result() as $c) { ?>
						
							<option value="<?= $c->categoryid ?>"><?= $c->category ?></option>
						
						<? } ?>
						
					</select>

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td></td>
				<td>
					
					
				</td>
	
			</tr>
			
			<tr>
			
				<td>Usergroup :</td>
				<td>
					
					<select name="usergroupid" id="usergroupid" class="searchField">
			
						<option value="0">SHOW ALL</option>
						
						<? foreach ($usergroups as $key=>$ug) { ?>
								
							<option value="<?= $key ?>"><?= $ug ?></option>
						
						<? } ?>
						
					</select>

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td></td>
				<td>
					
					
				</td>
	
			</tr>
		
			
			<tr>
			
				<td>Search Text :</td>
				<td>
					
						<input type="text" class="searchField" id="searchText" />

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Template :</td>
				<td>
					
					<select name="grabsheettemplateid" class="searchField">
						
						<option value="0">Select Template...</option>
						
						<? foreach ($grabsheetTemplates->result() as $g) { ?>
						
							<option value="<?= $g->grabsheettemplateid ?>" <? if ($grabsheet->grabsheettemplateid == $g->grabsheettemplateid) echo "SELECTED"; ?>><?= $g->templatename ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
	
			</tr>
			<tr>
			
				<td>App. Status :</td>
				<td>
					<select name="approvalStatusID" id="approvalStatusID" class="searchField">
						
						<option value="0">ALL</option>
								
						<? foreach ($approvalStatuses as $as) { ?>
				
							<option value="<?= $as['id'] ?>"><?= $as['status'] ?></option>
				
						<? } ?>
					
					</select>
					
					<br /><br />
				
				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Property Image <small>(optional)</small> :</td>
				<td>
					
					<select name="property_imageid" class="searchField">
						
						<option value="0" SELECTED>Select Property</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>" <? if ($grabsheet->property_imageid == $p->propertyid) echo "SELECTED"; ?>><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
						<br /><br />				
				</td>
	
			</tr>
			
			<tr>
			
				<td>Prod. Codes :</td>
				<td>
					<input type="checkbox" name="showProductCodes" <? if ($grabsheet->showproductcodes) echo "CHECKED"; ?>>
				
				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Branding :</td>
				<td>
					
					<select name="headerimageid" class="searchField">
												
						<? foreach ($headerImages->result() as $i) { ?>
						
							<option value="<?= $i->id ?>" <? if ($grabsheet->headerimageid == $i->id) echo "SELECTED"; ?>><?= $i->title ?></option>
						
						<? } ?>
						
					</select>
					
						<br /><br />				
				</td>
	
			</tr>
			
			<tr>
			
				<td colspan="2"><input type="button" value="Search" onclick="updateThumbs(1);" /></td>
				<td>&nbsp;&nbsp;</td>
				<td colspan="2" align="right"><input type="checkbox" name="savePermanent"/> Save permanently as file &nbsp;&nbsp;<input type="submit" name="button" value="Save Grabsheet"></td>
			
			</tr>
	
	</table>
	

	<input type="hidden" id="itemids" name="itemids">
	
	

</div>


<script type="text/javascript">
	
	
	/*Window.onDomReady(function() {
	
		changeSelect();
	
	});*/
	
	//window.addEvent('domready', changeSelect() );

		function stopRKey(evt) {
		  var evt = (evt) ? evt : ((event) ? event : null);
		  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
		}
		
		document.onkeypress = stopRKey;

	


</script>


