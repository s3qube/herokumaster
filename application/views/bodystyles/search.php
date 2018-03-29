

		
<? foreach ($bodystyles->result() as $b) { ?>

	<table border="0" class="searchProductTable">
		<tr>
			<td valign="middle">
				
				
				
				<span class="propertyName">
					
					<a href="<?= base_url(); ?>bodystyles/view/<?= $b->id?>"><?=$b->code ?> // <?= $b->bodystyle?></a><? if ($b->category) { ?> // (<?= $b->category ?>) <? } ?></span> 
					<br />
				
				<span class="searchProductInfo"></span>
			
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	
<? } ?>


		
		
		

	
	