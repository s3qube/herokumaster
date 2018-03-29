
<form name="permform" method="post" action="<?= base_url(); ?>offices/save/<?=$id?>">
	
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
	
				<h3 class="userField">Office Name:</h3>
								
				<input type="text" name="office" value="<?= $office->office?>" class="userField" />
			
				
				<br /><br />
	
				<div id="">
				
					<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
						<tr>
							<td class="boldHeader">Territories:</td>
							<td align="right"><!--<a href=""><img src="<?=base_url();?>/resources/images/inv_groupchk.gif" width="26" height="26" border="0"></a><a href="#"><img src="<?=base_url();?>/resources/images/inv_groupx.gif" width="26" height="26" border="0"></a>--></td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td></td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_grouptop.gif" width="304" height="7"></td>
						</tr>
						<tr>
							<td background="<?=base_url();?>/resources/images/inv_groupbg.gif">
								
								
								
								<?= $this->opm->displayOfficeTerritories($office->id); ?>								
								
						
						
							</td>
						</tr>
						<tr>
							<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
						</tr>
					</table>
					
					<br /><br />
					
				</div>
			
			
			</td>
		</tr>
	</table>

				

	<table border="0" class="editPerms">

		<tr>
			<td colspan="2" align="right"><br /><br /><input type="submit" value="save"></td>
		</tr>

	</table>
</form>
