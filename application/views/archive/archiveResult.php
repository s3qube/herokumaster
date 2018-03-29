

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	
	<tr>
	
		<td valign="top">
		
			<h1>Archive Complete!</h1>
			<br>
			<h3>Files Successfully Archived: <?= $goodArchiveCount ?></h3>
			<h3>Files With Errors: <?= $badArchiveCount ?></h3>
			<br><br>
			
			<p>Log:</p>
			
			<br>
			
			<? foreach ($log as $row) { ?>
			
				<?= $row ?><br />
			
			<? } ?>
			
			
		</td>
	
	</tr>
	
</table>