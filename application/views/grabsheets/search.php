

<? foreach ($grabsheets->result() as $g) { ?>
	
	
	<table border="0" class="searchProductTable" width="85%">
	
		<tr>
			<td><?= $g->grabsheettitle ?>&nbsp;&nbsp;//&nbsp;&nbsp;<?= $g->templatename ?><?= ($g->isfile) ? "&nbsp;&nbsp<em><span class=\"blueLink\" style=\"font-weight:normal;\"><small>(FILE)</small></span></em>" : null ?></td>
			
				<td align="right"><a href="<?= base_url(); ?>grabsheets/view/<?= $g->grabsheetid?>">View</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>grabsheets/view/<?= $g->grabsheetid?>/true">Download Hi-Res</a><? if (!$g->isfile) { ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?= base_url(); ?>grabsheets/view/<?= $g->grabsheetid?>/1/0/1">Download Lo-Res</a><? } ?>&nbsp;&nbsp;|&nbsp;&nbsp;<? if ($g->isfile) { ?><a href="<?= base_url(); ?>grabsheets/create/<?= $g->grabsheetid?>/true">Copy</a><? } else { ?><a href="<?= base_url(); ?>grabsheets/create/<?= $g->grabsheetid?>/">Edit</a><? } ?></td>
			
		</tr>
	
	</table>
	
	<? 
	
	//unset ($g->grabsheet);
	//print_r($g); 
	
	?>
	
	<div class="searchDiv"></div>
	
<? } ?>
