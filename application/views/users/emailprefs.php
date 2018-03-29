
<? //print_r($product) ?>

<form name="userform" action="<?= base_url(); ?>users/save" method="post" enctype="Multipart/Form-Data">
	<input type="hidden" name="userid" value="<?= $user->userid ?>">

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Properties:</h3>
					
					<select name="appPropertyID" MULTIPLE>
						
						<? foreach ($properties->result() as $p) { ?>
						
							<option value="<?= $p->propertyid ?>"><?= $p->property ?></option>
						
						<? } ?>
						
					</select>	
								
			</td>
			
			<td valign="top">
			
				<h3 class="userField">Email Types:</h3>
			
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
	