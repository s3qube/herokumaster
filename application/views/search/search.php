

<? foreach ($products->result() as $p) { ?>
	
	<table border="0" class="searchProductTable">
		<tr>
			<td><div class="prodSearchImageTN"><a href="<?=base_url();?>products/view/<?=$p->opm_productid?>" <? if ($p->default_imageid && checkPerms('can_view_visuals')) { ?> class="tipper" id="tipper_<?= $p->default_imageid?>" <? } ?>><? if (checkPerms('can_view_visuals')) { ?><img src="<?=base_url();?>imageclass/viewThumbnail/<?=$p->default_imageid?>/80" width="80" border="0"><? } else { ?><img src="<?=base_url();?>resources/images/x.gif" width="80" border="0"><? } ?></a> </div></td>
			<td valign="top">
				<span class="searchProductName"><? if (checkPerms('view_opmid_searchpage')) { ?><?= $p->opm_productid ?>&nbsp;&nbsp;-&nbsp;&nbsp;<? } ?><?= $p->property ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?= $p->productname ?><? if ($p->category) { ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?= $p->category ?><? } ?></span><br />
				<? if ($p->licenseecode && checkPerms('can_view_licenseecode')) { ?><span class="searchLicCode">Licensee Code: <?= $p->licenseecode ?><? if ($p->productcode && checkPerms('can_view_productcodes')) { ?>&nbsp;&nbsp;//&nbsp;&nbsp;Product Code: <?= $p->productcode ?><? } ?></span><br /><? } ?>
				<span class="searchProductInfo"><? if (checkPerms('can_view_productlines') && $p->productlines) { ?><?=$p->productlines?>&nbsp;&nbsp;//&nbsp;&nbsp;<? } ?>Created: <?= opmDate($p->timestamp) ?>&nbsp;&nbsp;//&nbsp;&nbsp;Last Activity: <?= opmDate($p->lastmodified) ?>&nbsp;&nbsp;//&nbsp;&nbsp;Status: <span class="prodSummaryAppRow_<?= $p->approvalstatusid ?>"><?= $p->approvalstatus ?></span></span><br />
				<? if (checkPerms('can_view_masterfile_sep_totals')) { ?>
					<span class="searchProductInfo"><?=$p->numMasterFiles?>&nbsp;Master Files&nbsp;&nbsp;//&nbsp;&nbsp;<?=$p->numSeparations?> &nbsp;Separations</span>
				<? } ?>
				<? if (checkPerms('can_view_sample_approval')) { ?>
					&nbsp;&nbsp;//&nbsp;&nbsp;<span class="searchProductInfo">Sample Approval:</span> <span class="prodSummaryAppRow_<?= $p->sampleappstatusid ?>"><?= $p->sampleappstatus ?></span>
				<? } ?>
				
				<? if (checkPerms('can_view_purchase_info') && $p->lastpurchaseid) { ?>
					&nbsp;&nbsp;//&nbsp;&nbsp;<span class="<?= ($p->lastpurchase_enddate < mktime()) ? "searchProductInfo" :  "searchProductInfoRed" ?>"><strong><?= ucwords($p->lastpurchase_pt_pasttense) ?> by: <?= $p->lastpurchase_account ?> on <?= opmDate($p->lastpurchase_timestamp) ?> <?= ($p->lastpurchase_isexclusive) ? "(EXCLUSIVE)" : "(NON-EXCLUSIVE)" ?></strong></span>
				<? } ?>
			</td>
		</tr>
	</table>
	
	<div class="searchDiv"></div>
	
<? } ?>

<div class="searchTooltip">
 
  <img src="<?= base_url() ?>resources/images/x.gif" id="searchTooltipImg" width="250" height="250" />
  
</div>


<script type="text/javascript">

	$(document).ready(function() {
		
		//opm.initializeTooltips();
				
		$(document).tooltip({ 
			
			items: ".tipper",
			content: function( callback ) {
			
			
						imgid = $(this).attr("id").substring(7);
						//alert(imgid);
						callback('<img src="' + base_url + 'imageclass/viewThumbnail/' + imgid + '/290" width="290" />');
					   /* if ( $(this).is('input') ) {
					      callback( 'foo' );
					    } else {
					      callback( 'bar' );  
					    }*/
					 },
			position: { my: "left+15 center", at: "right center" },
			
			open: function( event, ui ) {
				
				//alert(ui);
				
				//imgid = ui.attr("id").substring(7);
				//(new Image()).src = '<img src="' + base_url + 'imageclass/viewThumbnail/' + imgid + '/290';
				
				//alert("preloading img for tooltip id :" . imgid );
				
			}
			
		});
		
		//var content = $( ".selector" ).tooltip( "option", "content" );
		
		
		/*$(".tipper").tooltip({ 
			
			tip:'.searchTooltip',
			position: "center right", 
			effect: "fade",
			
			onBeforeShow: function() {
  				
  				obj = this.getTrigger();
				imageid = obj.attr("id").substring(7);
  				  				
  				src = base_url + "imageclass/viewThumbnail/" + imageid + "/290";
  				$('#searchTooltipImg').hide().attr("src", src).fadeIn('fast');
  				
  			
  			}
		}); */
	
	});

</script>