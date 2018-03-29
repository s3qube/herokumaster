
<? // print_r($children); ?>

<div id="usergroup_<?= $usergroupid ?>">

	<table border="0" cellpadding="0" cellspacing="0" width="<?= $table_width ?>" align="center">
		
		<tr id="invGroupRowTr_<?=$usergroupid?>" class="<? if ($isassigned == true) echo "invGroupRow_active"; else echo "invGroupRow"; ?>">
			<td style="width:5px;"><? if ($has_children) { echo "&nbsp;+"; } ?></td>
			<td style="width:51px;"><? if ($has_children) { ?><a href="javascript:opm.showHideDiv('usergroup_<?= $usergroupid ?>_children');"><? } ?><img src="<?=base_url();?>/resources/images/<? if ($isassigned == true) echo "inv_groupicon"; else echo "x"; ?>.gif" width="51" height="36" border="0" id="invGroupRowImg_<?=$usergroupid?>"><? if ($has_children) { ?></a><? } ?></td>
			<td id="invGroupRowTd_<?=$usergroupid?>" class="<? if ($isassigned == true) echo "invGroupText_active"; else echo "invGroupText"; ?>"><?= $usergroup ?></td>
			<td align="right">
				<? if ($usergroupid != $this->config->item('separatorsGroupID') && $usergroupid != $this->config->item('screenprintersGroupID')) { ?>
					<input type="checkbox" name="" class="inv_chkbox" <? if ($isassigned == true) echo "checked='true'"; ?> onClick="changeGroup(<?= $opm_productid ?>,<?=$usergroupid?>,this.checked);" />
				<? } ?>
			</td>
		</tr>
		
		<tr>
			<td colspan="4"><img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1"></td>
		</tr>
	</table>

</div>