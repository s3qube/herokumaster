
<script language="javascript">

	var defaultImageIDs = Array();

	$(document).ready( function() {
	
	});
	
	function swapMatchImage(key,opm_productid) {
	
		var src = "<?= base_url()?>imageclass/viewThumbnail/" + defaultImageIDs[opm_productid] + "/250";
		
		//alert(src);
	
		$("#navisionMatchImage_" + key).attr("src", src);
	
	}
	
	function assignDesignCode(key,designcode) {
	
		var assignType = $('input:radio[name=assignType_' + key + ']:checked').val();
				
		if (assignType == 'byMatches') {
			
			var opm_productid = $("#opm_productid_" + key).val();
		
		} else {
		
			var opm_productid = $("#manualOpmProductID_" + key).val();
			
		}
		
		//alert("assigning designcode:" + designcode + " to opmpid: " + opm_productid);
		
		
		$.post('<?= base_url(); ?>navisionimport/assignDesignCode/', { opm_productid: opm_productid, designcode: designcode, propertyid: "<?= $p->propertyid ?>" }, function(data) {
				
							
			if (data == 'success') {
			
				$("#productMatchRow_"+key).fadeOut("slow");
				
			
			} else {
			
				alert(data);
			
			}
	
		});
		
	
	}

</script>

<form name="importFileForm" action="<?= base_url(); ?>navisionImport/importProducts" method="post" enctype="Multipart/Form-Data">


	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h1>Match <?= $p->property ?> Products</h1>
					
					<? if ($numAssigned) { ?>
					
						<h3>(<?= $numAssigned ?> products auto-assigned)</h3>
					
					<? } ?>
					
					<? foreach($navProducts->result() as $key=>$np) { ?>
					
						<div class="productMatchRow" id="productMatchRow_<?= $key ?>">
						
							<table border="0" width="760">
							
								<tr>
									
									<td valign="top">	
									
										<table border="0">
										
											<tr>
												<th align="right">ItemCode:</th>
												<td><?= $np->itemcode ?>
											</tr>
											<tr>
												<th align="right">ItemCode2:</th>
												<td><?= $np->itemcode2 ?></td>
											</tr>
											
											<tr>
												<th align="right">Description:</th>
												<td><?= $np->description ?></td>
											</tr>
											
											<tr>
												<th align="right">Description2:</th>
												<td><?= $np->description2 ?></td>
											</tr>
											<tr>
												<th align="right">Color:</th>
												<td><?= $np->color ?></td>
											</tr>
											<tr>
												<th align="right">Body Style:</th>
												<td><?= $np->bodystyle ?></td>
											</tr>
										
											<tr>
											
												<td colspan="2"><br/><br/><input type="button" value="Assign Design Code" class="matchAssignBtn" onclick="assignDesignCode(<?= $key ?>, '<?= substr($np->itemcode,4,4) ?>')" /></td>
												
											</tr>
										
										</table>
									
									</td>
									<td align="right">
										
										<input type="hidden" name="designCodes[<?= $key ?>]" value="<?= substr($np->itemcode,4,4) ?>" />
										
										<div align="left" style="padding-left:32px; margin-bottom:10px;"><input type="radio" name="assignType_<?= $key ?>" id="assignType_<?= $key ?>" value="byMatches" CHECKED /> pick product from matches.</div>
										
										<select name="opm_productid_<?= $key ?>" id="opm_productid_<?= $key ?>" class="matchSelect" onchange="swapMatchImage(<?= $key ?>,this.value);">
										
											<? foreach ($np->matches as $p) { ?>
											
												<option value="<?= $p->opm_productid ?>"><?= $p->productname ?> - <?= $p->category ?></option>
											
											<? } ?>
										
										</select>
										
										<!-- RECORD DEFAULT IMAGE IDS FOR SWAPPAGE (a bit kludgey but so what?) -->
										
										<script language="javascript">
										
											<? foreach ($np->matches as $p) { ?>
												
												defaultImageIDs[<?= $p->opm_productid ?>] =  <?= $p->default_imageid ?>;												
											
											<? } ?>
										
										</script>
									
										<br />
								
										<? if ($np->matches) { ?>
									
											<img src="<?= base_url(); ?>imageclass/viewThumbnail/<?= $np->matches[0]->default_imageid ?>/250" width="250" height="250" id="navisionMatchImage_<?= $key ?>" class="navisionMatchImage" />
									
										<? } else { ?>
											
											<img src="<?= base_url(); ?>resources/images/x.gif" width="250" height="250" class="navisionMatchImage" />
										
										<? } ?>
										
										<div align="left" style="padding-left:32px; margin-top:20px; margin-bottom:10px;"><input type="radio" name="assignType_<?= $key ?>" id="assignType_<?= $key ?>" value="byID" /> assign to this opm_productid <input type="text" id="manualOpmProductID_<?= $key ?>" size="8" />.</div>
								
									</td>
								</tr>
								
								
							
							</table>
						
						
							<hr />
						
						</div>	
					
					
						

					
					
					<? } ?>
	
			
			</td>
			
		</tr>
	</table>
	
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="right"><input type="submit" value="Save">&nbsp;&nbsp;&nbsp;</td>
		</tr>
	</table>
	

</form>

<br />	
	