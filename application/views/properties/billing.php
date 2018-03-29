
<? //print_r($product) ?>

<script language="javascript">

	

</script>

<form name="userform" action="<?= base_url(); ?>properties/saveBilling" method="post" enctype="Multipart/Form-Data">
	
	<? if (isset($p)) { ?>
	
		<input type="hidden" name="propertyid" value="<?= $p->propertyid ?>">

	<? } ?>

	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top" width="540">
					
					<h3 class="userField">Recoupable Percentages</h3>
					
					<table border="0">
				
						<? foreach ($channels->result() as $c) { ?>
							
							<tr>
							
								<td><?= $c->channel ?></td>
								<td><input type="text" maxlength="3" style="width:70px;" name="channelPercentage[<?= $c->channelcode ?>]" value="<?= $c->rate ?>" class="userField" />%</td>
							
							</tr>
						
						<? } ?>			
				
				</table>
			
			</td>
			<td valign="top" align="left">
				
				
			</td>
		</tr>
	</table>
	
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="right"><input type="submit" value="Save" class="invoiceBtn">&nbsp;&nbsp;&nbsp;</td>
		</tr>
	</table>
	
	<div style="clear:both;"></div>
	

</form>

<br />	
	