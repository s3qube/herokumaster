

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			
			<div id="inv_groups">
				<form name="emailForm" method="post" action="<?=base_url();?>users/sendEmailHandler">
				<table border="0" cellpadding="0" cellspacing="0" style="margin-left:6px;" width="290" height="30">
					<tr>
						<td class="boldHeader">Email Recipients:</td>
						<td align="right"><!--<a href="javascript:changeGroup(<?=$product->opm_productid?>,1,1);"><img src="<?=base_url();?>/resources/images/inv_groupchk.gif" width="26" height="26" border="0"></a><a href="#"><img src="<?=base_url();?>/resources/images/inv_groupx.gif" width="26" height="26" border="0"></a>--></td>
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
							
							<?= $this->opm->displayUsergroups(0,'users/email_usergroup.php'); ?>								

						</td>
					</tr>
					<tr>
						<td><img src="<?=base_url();?>/resources/images/inv_groupbtm.gif" width="304" height="7"></td>
					</tr>
				</table>
				
				
			</div>	
			
			<br />
			
			<div class="boldHeader" style="margin-bottom:5px;">Email Subject:</div>
			
			<input type="text" class="userField" style="width:400px;" name="subject" />
			
			<br /><br />
			
			<div class="boldHeader" style="margin-bottom:5px;">Email Body:</div>
			
			<textarea class="userField" style="width:400px;height:200px;" name="body"></textarea>
			
			<br /><br />
			
			<input type="submit" class="invoiceBtn" value="Send Email" />
			
			<br /><br />
			
			</form>
			
		</td>
		
			
	</tr>
</table>






	
	