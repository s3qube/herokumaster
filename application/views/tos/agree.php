<?

	//print_r($stats);
	
	

?>	

<script language="javascript">


	function checkForm() {
		
		isChecked = $('#tosagree').attr('checked');
		
		if (isChecked == 'checked') {
		
			return true;
					
		} else {
		
			alert("You need to check the box to continue.");
			return false;
		
		}
			
			
	}


	


</script>
					


<table width="600" align="center">
	
	<form name="tosForm" method="post" action="<?= base_url(); ?>tos/handle" onsubmit="return checkForm();">
		
		<input type="hidden" name="tosids" value="<?= $strTosIDs ?>" />
	
		<tr>
		
			<td><strong>Please agree to the OPM terms of service before continuing.</strong><br /><br /></td>
		
		</tr>
		
		<tr>
		
			<td>
				<div class="tosTextArea"><?= $tosText ?></div>
			</td>
		
		</tr>
		
		<tr>
			<td><br /><input type="checkbox" id="tosagree" name="tosagree" /> By clicking this box, you agree to the following terms and conditions.</td>
		</tr>
	
		<tr>
			<td><br /><input type="submit" name="proceed" class="invoiceBtn" value="Proceed to OPM" /></td>
		</tr>
	
	</form>

</table>