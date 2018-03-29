
<div id="right_<?=$rightid?>">

	<table border="0" cellpadding="0" cellspacing="0" width="290" align="center">
		
		<tr id="invRightRowTr_<?=$rightid?>" class="<? if (($isassigned || $isdefault) && !$isexception) echo "invGroupRow_active"; else echo "invGroupRow"; ?>">
			<td style="width:51px;"><img src="<?=base_url();?>/resources/images/<? if ($isassigned || $isdefault && !$isexception) echo "inv_righticon"; else echo "x"; ?>.gif" width="51" height="36" border="0" id="invRightRowImg_<?=$rightid?>"></a></td>
			<td width="151" style="width:151px;" id="invRightRowTd_<?=$rightid?>" class="<? if ($isassigned || $isdefault && !$isexception) echo "invGroupText_active"; else echo "invGroupText"; ?>"><?= $right ?></td>
			
			<? if ($isdefault == true && $isexception == false) { ?>
				<td align="right"><input type="checkbox" name="" class="inv_chkboxDIS" checked="true" DISABLED /></td><td class="rightExceptionTD" align="center"><a href="<?= base_url(); ?>products/rightException/<?= $opm_productid ?>/<?=$rightid?>" class="rightException" title="Create Exception">x</a></td>
			<? } else if ($isexception == true) { ?>
				<td align="right"><input type="checkbox" name="" class="inv_chkboxDIS" DISABLED /></td><td class="terrExceptionTD" align="center"><a href="<?= base_url(); ?>products/rightExceptionCancel/<?= $opm_productid ?>/<?=$rightid?>" class="rightExceptionCancel" title="Cancel Exception">&#10004;</a></td>
			<? } else { ?>
				<td align="right"><input type="checkbox" name="" class="inv_chkbox" <? if ($isassigned == true) echo "checked='true'"; ?> onClick="changeRight(<?= $opm_productid ?>,<?=$rightid?>,this.checked);" /></td>
			<? } ?>
		</tr>
		
		<tr>
			<td colspan="4"><img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1"></td>
		</tr>
		
	</table>

</div>