
<div id="searcharea">

	<form name="grabsheet" method="post" action="<?=base_url();?>grabsheets/save" onsubmit="return checkGrabsheetForm();">

	<table border="0" cellpadding="3" width="95%">
			<tr>
				<td>Property :</td>
				<td>
					
					<select name="propertyid" id="propertyid" onChange="changeSelect();" class="searchField">
						
						<option value="0" SELECTED>Select Property</option>
						
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
						
							<option value="<?= $g->grabsheetgroupid ?>"><?= $g->grabsheetgroup ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
				
				
			</tr>
			
			<tr>
			
				<td>Product Line :</td>
				<td>
					
						<div id="sDiv">
		
							<select name="productlineid" id="productLineSelect" class="searchField">
								
								<option value="">---</option>
							
							</select>
							
						</div>

				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Title :</td>
				<td>
					
					<input type="text" name="title" class="searchField">
					
				</td>
	
			</tr>
			<tr>
			
				<td></td>
				<td>


				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Template :</td>
				<td>
					
					<select name="grabsheettemplateid" class="searchField">
						
						<option value="0">Select Template...</option>
						
						<? foreach ($grabsheetTemplates->result() as $g) { ?>
						
							<option value="<?= $g->grabsheettemplateid ?>"><?= $g->templatename ?></option>
						
						<? } ?>
						
					</select>
					
				</td>
	
			</tr>
			<tr>
			
				<td></td>
				<td>


				</td>
				<td>&nbsp;&nbsp;</td>	
				
				<td>Property Image <small>(optional)</small> :</td>
				<td>
					
					<select name="property_imageid" class="searchField">
						
						<option value="0" SELECTED>Select Property</option>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
						
						<? } ?>
						
					</select>
					
										
				</td>
	
			</tr>
	
	</table>
	

	<input type="hidden" id="itemids" name="itemids">
	<input type="submit" name="button" value="Save Grabsheet">
	

</div>


<script type="text/javascript">
	
	
	Window.onDomReady(function() {
	
		changeSelect();
	
	});
	


</script>


