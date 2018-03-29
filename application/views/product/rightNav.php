
	

	<? if (checkPerms('view_forum_tab') && isset($newCommments)) { ?>
	++ New Comments since last view<br />
	<? } ?>
	
	
	<? if (checkPerms('view_history_tab') && isset($newHistory)) { ?>
	++ New History since last view
	<? } ?>
	
	<? if (checkPerms('view_lock_status')) { ?>
		
		<div class="productLock">
			<? if ($product->islocked) { ?>
				<img src="<?= base_url(); ?>resources/images/lockIcon.png" alt="lockIcon" width="26" height="23" id="productLock" />
			<? } else { ?>
				<img src="<?= base_url(); ?>resources/images/lockIconOpen.png" alt="lockIcon" width="26" height="23" id="productLock" />
			<? } ?>
		</div>
	
	<? } ?>