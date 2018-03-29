		

<? $curDesigner = ""; ?>

	
	<table border="0" class="searchProductTable">
		<tr>
			<td>
			
			
			
				<? 
				
					foreach ($designerQueue->result() as $dq) { 
					
				?>
					<? if ($dq->userid != $curDesigner) { ?><h3><a href="#"><?= $dq->username ?></a></h3><? } ?>
				
						&nbsp;&nbsp;&nbsp;<?= $dq->property ?> - <?= $dq->productname ?> - Due On:<?= date("m/d/y",$dq->duedate) ?><br />
				
				<? 
						$curDesigner = $dq->userid;
					} 
					
				?>
				
				
				
			
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	



		
		
		

	
	