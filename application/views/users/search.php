

<? foreach ($users->result() as $u) { ?>
	<table border="0" class="searchProductTable">
		<tr>
			<td><a href="<?=base_url();?>users/view/<?=$u->userid?>"><? $this->opm->displayAvatar($u->userid); ?></a></td>
			<td valign="middle">
				<span class="searchProductName"><a href="<?=base_url();?>users/view/<?=$u->userid?>"><?= $u->username ?></a></span>&nbsp;&nbsp;//&nbsp;&nbsp;<?= $u->usergroup ?><? if (isset($u->usergroup2)) echo ", " . $u->usergroup2; ?>&nbsp;&nbsp;//&nbsp;&nbsp;<a href="mailto:<?=$u->login ?>"><?= $u->login ?></a><? if (!$u->isactive) {  ?>&nbsp;&nbsp;//&nbsp;&nbsp; <font color="red">DEACTIVATED</font><? } ?><br />
			</td>
		</tr>
	</table>
	<div class="searchDiv"></div>
<? } ?>

