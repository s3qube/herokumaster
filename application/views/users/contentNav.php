<div class="contentNavArea">
	
	<table cellpadding="0" cellspacing="0" border="0" height="30" id="contentNavTable">
	
		<tr>
			
			<td id="contentTab1" class="tabNavOn"><a href="#" id="contentTabLink1" class="tabNavOn" onclick="return false;" onmousedown="opm.changeContent(1,'basicinfo','users'); this.blur(); return false;">Basic Info</td>
			
			<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<td id="contentTab2" class="tabNavOff"><a href="#" id="contentTabLink2" class="tabNavOff" onclick="return false;" onmousedown="opm.changeContent(2,'approvalProperties','users'); this.blur(); return false;">Approval Properties</a></td>
			
			<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<td id="contentTab3" class="tabNavOff"><a href="#" id="contentTabLink3" class="tabNavOff" onclick="return false;" onmousedown="opm.changeContent(3,'invoicing','users'); this.blur(); return false;">Invoicing</a></td>
			
			<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? if (checkPerms('can_edit_user_permissions')) { ?>
			
				<td id="contentTab4" class="tabNavOff"><a href="#" id="contentTabLink4" class="tabNavOff" onclick="return false;" onmousedown="opm.changeContent(4,'permissions','users'); this.blur(); return false;">Permissions</a></td>
			
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
	
		</tr>
		
	</table>
	
</div>