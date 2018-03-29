

		
<? foreach ($genres->result() as $g) { ?>

	<table border="0" class="searchProductTable">
		<tr>
			<td valign="middle">
				
				<span class="propertyName">
					
					<a href="<?= base_url(); ?>genres/view/<?= $g->id?>"><?= $g->genre?></a></span> 
					<br />
				
				<span class="searchProductInfo"></span>
			
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	
<? } ?>


		
		
		

	
	