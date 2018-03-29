<div class="contentNavArea">
	<table cellpadding="0" cellspacing="0" border="0" height="30" id="contentNavTable" class="contentNavTable">
		<tr>
			
			<? if (checkPerms('view_summary_tab')) { ?>
			
				<td id="contentTab1" class="<?= ($tabname == "summary" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink1" class="<?= ($tabname == "summary" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(1,'summary','products'); this.blur(); return false;">Summary</td>
				
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
		
			<? if (checkPerms('view_involvement_tab')) { ?>
			
				<td id="contentTab2" class="<?= ($tabname == "involvement" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink2" class="<?= ($tabname == "involvement" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(2,'involvement','products'); this.blur(); return false;">Involvement</a></td>
				
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
			
			<? if (checkPerms('view_images_tab')) { ?>
			
				<td id="contentTab3" class="<?= ($tabname == "images" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink3" class="<?= ($tabname == "images" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(3,'images','products'); this.blur(); return false;">Images / Files</a></td>
				
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
			
			<? if (checkPerms('view_billing_prodtab')) { ?>
			
				<td id="contentTab4" class="<?= ($tabname == "billing" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink4" class="<?= ($tabname == "billing" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(4,'billing','products'); this.blur(); return false;">SKUs / Billing</a></td>
				
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
			
			<? if (checkPerms('view_forum_tab')) { ?>
			
				<td id="contentTab5" class="<?= ($tabname == "forum" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink5" class="<?= ($tabname == "forum" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(5,'forum','products'); this.blur(); return false;">Comments</a></td>
				
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>
			
			<? } ?>
			
			
			<? if (checkPerms('view_history_tab')) { ?>
			
				<td id="contentTab6" class="<?= ($tabname == "history" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink6" class="<?= ($tabname == "history" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(6,'history','products'); this.blur(); return false;">History</a></td>
			
				<td class="tabSpacer"><img src="<?=base_url();?>resources/images/x.gif" width="1" height="30"></td>

			
			<? } ?>
			
			
			<? if (checkPerms('view_wholesale_tab')) { ?>
			
				<td id="contentTab7" class="<?= ($tabname == "wholesale" ? "tabNavOn" : "tabNavOff") ?>"><a href="#" id="contentTabLink7" class="<?= ($tabname == "wholesale" ? "tabNavOn" : "tabNavOff") ?>" onclick="return false;" onmousedown="opm.changeContent(7,'wholesale','products'); this.blur(); return false;">Wholesale</a></td>
			
			<? } ?>
		
		</tr>
	</table>
</div>