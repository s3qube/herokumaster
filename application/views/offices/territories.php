
<div id="territory_<?=$territoryid?>">

	<table border="0" cellpadding="0" cellspacing="0" width="290" align="center">
		
		<tr id="invTerritoryRowTr_<?=$territoryid?>" class="<? if ($isassigned == true || $isinherited == true) echo "invGroupRow_active"; else echo "invGroupRow"; ?>">
			<td style="width:51px;"><img src="<?=base_url();?>/resources/images/<? if ($isassigned == true || $isinherited == true) echo "inv_territoryicon"; else echo "x"; ?>.gif" width="51" height="36" border="0" id="invTerritoryRowImg_<?=$territoryid?>"></a></td>
			<td id="invTerritoryRowTd_<?=$territoryid?>" class="<? if ($isassigned == true || $isinherited == true) echo "invGroupText_active"; else echo "invGroupText"; ?>"><?= $territory ?></td>
			<td align="right"><input type="checkbox" <? if ($isinherited == true) { ?> disabled="disabled" <? } ?> name="" class="inv_chkbox" <? if ($isassigned == true || $isinherited == true) echo "checked='true'"; ?> onClick="changeOfficeTerritory(<?= $officeid ?>,<?=$territoryid?>,this.checked);" /></td>
		</tr>
		
		<tr>
			<td colspan="3"><img src="<?=base_url();?>/resources/images/inv_grouprowsep.gif" width="209" height="1"></td>
		</tr>
		
	</table>

</div>