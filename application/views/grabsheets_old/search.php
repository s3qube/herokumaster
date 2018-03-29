

<? foreach ($grabsheets->result() as $g) { ?>
	
	<table border="0" class="searchProductTable" width="85%">
		<tr>
			<td><?= $g->grabsheettitle ?>&nbsp;&nbsp;//&nbsp;&nbsp;<?= $g->templatename ?></td>
			<td align="right"><a href="<?= base_url(); ?>grabsheets/view/<?= $g->grabsheetid?>">View</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>grabsheets/view/<?= $g->grabsheetid?>/true">Download</a></td>
		</tr>
	</table>
	
	<? 
	
	//unset ($g->grabsheet);
	//print_r($g); 
	
	?>
	
	<div class="searchDiv"></div>
	
<? } ?>
