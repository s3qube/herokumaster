
<!--<a href="#" onmouseover="showGsTooltip(this,1234);" onmouseout="hideGsTooltip();">Show TT</a>-->


<form name="gsForm" id="gsForm" action="">
	<input type="hidden" name="strGS" id="strGS" value="" />
</form>
<table width="95%" border="0" cellpadding="0" cellspacing="0">

	<tr>
		<td valign="top">
			
			<div class="grabPag" id="grabPag">
				
				Page <span id="pagPageNum" title=""></span> of <span id="pagTotalPages"></span> (<span id="pagTotalProds"></span> Products)&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="pagPageBack(); return false;">&lt;</a>&nbsp;&nbsp;<a href="#" onclick="pagPageForward(); return false;">&gt;</a>
				
			</div>
			
			<div class="items">
			
				<ul id="sort">
					
					
				
				</ul>
				
			</div>
			
			<div id="moveAllBtn" style="visibility:hidden;">
			
				<input type="button" value="Move All >>" onclick="gsMoveAll();" />
			
			</div>
		
		</td>
		
		<td valign="top">
			
				<div class="items">
				
					<ul id="sort2" style="width:300px;min-height:400px;border: 1px solid #cccccc;">
						
						<? if (isset($grabsheet->items)) { ?>
							
							<? foreach ($grabsheet->items as $i) { ?>
							
								<li id="gsItem_<?= $i->imageid ?>" class="gsItem" onmousedown="killGsTooltip();" onmouseover="showGsTooltip(this,<?= $i->imageid ?>);" onmouseout="hideGsTooltip();" alt="<?= $i->imageid ?>" background-image="<?= base_url(); ?>imageclass/viewThumbnail/<?= $i->imageid ?>" style="background-image: url(<?= base_url() ?>imageclass/viewThumbnail/<?= $i->imageid ?>/30);"><?= $i->property ?>  //  <?= $i->productname ?> <?= ($i->approvalstatus == 'Approved' || $i->approvalstatus == 'Approved W/ Revisions') ? "&nbsp;&nbsp;<span style='color:green;font-weight:bold;font-size:12px;'>&#10004;</span>" : null ?>&nbsp;&nbsp;<a href='#' onclick="setGSCommentID(<?= $i->imageid ?>); return false;" class="blueLink">&#9998;</a></li>
							
							<? } ?>
						
						<? } else { ?>
					
							<li class="gsInitialItem" id="dropInstruction">Drop Items Here</li>
					
						<? } ?>
					
					</ul>	
				
				</div>
				
				<div class="trash">
					
					<ul id="trash" style="width:75px;min-height:75px;border: 1px solid #cccccc; ">
						<li class="gsTrash" title="drag an item to the trash to delete it, or click on the trash to delete all items in the current grab" id="trashImage" style="width:100px; height:100px; background-image:url(<?= base_url(); ?>resources/images/trash.gif) 0 0 no-repeat;"></li>
					</ul>
				</div>
				
				

		
		</td>
	</tr>

</table>

<input type="hidden" id="gsCommentID" value="" />

<div id="gsTooltipDiv" style="display:none;position:absolute; top:10px; left:245px; width:324px;height:324px;background: url('<?= base_url(); ?>resources/images/tt_bg.png') 0 0 no-repeat">

	<img id="gsTooltipDivImg" src="<?= base_url();?>resources/images/500500placeholder.jpg" width="250" height="250" border="0" style="padding-top:37px;padding-left:37px;" />	

</div>

<div id="gsCommentWin" style="display:none">
	
	<form>

		Comment:<br />
		
		<textarea id="gsProdComment"></textarea> <br />
		
		<input type="button" value="Save &amp; Close" onclick="gsCommentSaveClose(this);" />
	
	</form>

</div>

<script type="text/javascript">

	var $currentProductLineID = 0;

	window.addEvent('domready', function() {
		
		//opm.initializeTooltips();
	
	});

</script>