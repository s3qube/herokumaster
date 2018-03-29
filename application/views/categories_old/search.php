
		
<? foreach ($categories->result() as $p) { ?>

	<table border="0" class="searchProductTable">
		<tr>
			<td valign="middle">
				
				
				
				<span class="propertyName">
					
					<a href="<?= base_url(); ?>categories/view/<?= $p->categoryid?>"><?= $p->category?></a></span>
					<br />
				
				<span class="searchProductInfo"></span>
			
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	
<? } ?>


		
		
		

	
	