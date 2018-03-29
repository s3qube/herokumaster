
<script language="javascript">

	$(document).ready( function() {
	
		$('#propertySelect').change(function() {

  			// set the window's location property to the value of the option the user has selected
 			 window.location = "<?= base_url() ?>navisionImport/matchProducts/" + $(this).val();
		
		});
	
	});

</script>

<form name="importFileForm" action="<?= base_url(); ?>navisionImport/importProducts" method="post" enctype="Multipart/Form-Data">


	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
				<select name="propertyid" class="userField required" id="propertySelect" onselect="redirectToProp()">
						
					<option value="">Please Select...</option>
					
					<? foreach ($properties->result() as $p) { ?>
					
						<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
					
					<? } ?>
					
				</select>	
			
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
			
				
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
	