
		
		<!--<h1>Here is the list of all the properties</h1>-->
		
		<!--<table>
			
					
		<? foreach ($properties->result() as $p) { ?>
		
			<tr>
			
			<td><?= $p->property ?></td>
			</tr>
		
		<? } ?>
		
		</table>-->
		
<? foreach ($properties->result() as $p) { ?>

	<table border="0" class="searchProductTable">
		<tr>
			<td valign="middle">
				
				<a href="<?= base_url(); ?>properties/view/<?= $p->propertyid?>"><? $this->opm->displayPropertyImage($p->propertyid); ?></a>
				
				<span class="propertyName">
					
					<a href="<?= base_url(); ?>properties/view/<?= $p->propertyid?>"><?= $p->property ?></a></span>
					<span class="propertyDetail">&nbsp;&nbsp;//&nbsp;&nbsp;Number Of Products: <?= $p->numProducts ?></span>
					
					<? if (checkPerms('can_view_all_files_list')) { ?>
					
						&nbsp;&nbsp;//&nbsp;&nbsp;<a href="<?= base_url() ?>properties/showAllFiles/<?= $p->propertyid ?>">View All Files</a>
					
					<? } ?>
					
					<? if (!$p->isactive) { ?>
					
						&nbsp;&nbsp;//&nbsp;&nbsp; <span style="color:red;">DEACTIVATED</span>
					
					<? } ?>
					
					<br />
				
				<span class="searchProductInfo"></span>
			
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	
<? } ?>


		
		
		

	
	