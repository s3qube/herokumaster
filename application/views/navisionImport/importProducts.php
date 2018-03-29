
<form name="importFileForm" action="<?= base_url(); ?>navisionImport/importProducts" method="post" enctype="Multipart/Form-Data">


	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Choose File To Import.</h3>
					
					<input type="file" name="importFile" class="userField" />
					
					<br /><br />
					
					File should be of the following format:<br />
					- csv, no quotes<br />
					- fields in order : itemcode1,itemcode2,description1,description2,itemsize,itemcolor,itembodystyle
					
			<br /><br />
						
					
			
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
	