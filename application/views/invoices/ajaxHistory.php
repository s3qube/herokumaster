
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
	</head>
	<body bgcolor="#ffffff">

		
		<table border="1" width="85%" align="center" cellpadding="4">

			
		<? foreach ($history->result() as $h) { ?>
		
			<tr>
				<td class="invHistoryRed"><?= opmDateTime($h->timestamp); ?></td>
				<td class="invHistory"><?= $h->event ?></td>
			</tr>
			
		<? } ?>
	
</table>
		
	</body>
</html>