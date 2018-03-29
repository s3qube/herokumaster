		
	<? foreach ($thumbnails->result() as $img) { ?>
		
		
		
			<div class="item" id="<?=$img->imageid?>" style="background-image:url(<?=base_url();?>imageclass/viewThumbnail/<?=$img->imageid?>)">
				<a href="" class="tipper" title="AJAX:<?=base_url();?>tooltips/grabsheetTip/<?=$img->imageid?>"><img src="<?=base_url();?>resources/images/x.gif" width=60 height=60 border=0 /></a>
			</div>
						
	
	<? } ?>
