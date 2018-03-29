
<div id="right_<?=$rightid?>">

	<table border="0" cellpadding="0" cellspacing="0" width="290" align="center">
		
		<tr id="invRightRowTr_<?=$rightid?>" class="<? if ($isassigned == true) echo "invGroupRow_active"; else echo "invGroupRow"; ?>">
			<td style="width:51px;"><img src="<?=base_url();?>/resources/images/<? if ($isassigned == true) echo "inv_righticon"; else echo "x"; ?>.gif" width="51" height="36" border="0" id="invRightRowImg_<?=$rightid?>"></a></td>
			<td id="invRightRowTd_<?=$rightid?>" class="<? if ($isassigned == true) echo "invGroupText_active"; else echo "invGroupText"; ?>"><?= $right ?></td>
			<td align="right"><input type="checkbox" name="" class="inv_chkbox" <? if ($isassigned == true) echo "checked='true'"; ?> onClick="changePropertyRight(<?= $propertyid ?>,<?=$rightid?>,this.checked);" /></td>
		</tr>
		
		<tr>
			<td colspan="3"><img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1"></td>
		</tr>
		
	</table>

</div>