<div class="contentNavArea">

	<table cellpadding="0" cellspacing="0" border="0" height="30" id="contentNavTable">
	
		<tr>
			
			<td id="contentTab1" class="tabNavOn"><a href="#" id="contentTabLink1" class="tabNavOn" onclick="return false;" onmousedown="opm.changeContent(1,'basicinfo','properties'); this.blur(); return false;">Basic Info</td>
			
			<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? if (checkPerms('can_view_productlines')) { ?>
			
				<td id="contentTab2" class="tabNavOff"><a href="#" id="contentTabLink2" class="tabNavOff" onclick="return false;" onmousedown="opm.changeContent(2,'productLines','properties'); this.blur(); return false;">Product Lines</a></td>
			
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
			<? if (checkPerms('can_manage_property_genres')) { ?>
			
				<td id="contentTab3" class="tabNavOff"><a href="#" id="contentTabLink3" class="tabNavOff" onclick="return false;" onmousedown="opm.changeContent(3,'genres','properties'); this.blur(); return false;">Genres</a></td>
			
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
			<? if (checkPerms('can_view_properties_billing_tab')) { ?>
			
				<td id="contentTab4" class="tabNavOff"><a href="#" id="contentTabLink4" class="tabNavOff" onclick="return false;" onmousedown="opm.changeContent(4,'billing','properties'); this.blur(); return false;">Billing</a></td>
			
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
		</tr>
		
	</table>
	
</div>