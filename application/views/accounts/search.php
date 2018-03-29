

<? foreach ($accounts->result() as $a) { ?>
	<table border="0" class="searchProductTable" width="400">
		<tr>
			<td><a href="<?=base_url();?>accounts/view/<?=$a->accountid?>"><span class="searchProductName"><?= $a->account ?></span></a><? if (!$a->isactive) {  ?>&nbsp;&nbsp;//&nbsp;&nbsp; <font color="red">DEACTIVATED</font><? } ?></td>
			<td valign="middle" align="right">
				<!--<a href="<?=base_url();?>accounts/view/<?=$a->accountid?>" class="blueLink">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;--><a href="<?=base_url();?>accounts/toggleActivation/<?=$a->accountid?>" class="blueLink"><?= ($a->isactive) ? "Disable" : "Enable" ?></a>
			</td>
		</tr>
	</table>
	<div class="searchDiv"></div>
<? } ?>

