
<script type="text/javascript">

<?// if (checkPerms('can_post_forum_images')) { ?>


	tinymce.init({
	    selector: "#commentPostTxtArea",
	    theme: "advanced",
	    plugins : "jbimages",
	    language : "en",
	    width: 400,
		 
		//theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		//theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons1 : "jbimages",
		
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
	 
		relative_urls : false
	    
	    
	});

<? //} ?>

$(document).ready(function() {
	
	$('.prodSummaryForumPost img').each(function(){

	    if($(this).width()>500){
	      //  $(this).width(600).css('cursor','pointer').click(function(){$(this).css('width','');});
	        $(this).css('width','500');
	        $(this).wrap("<a href='" + $(this).attr("src") + "' />");
	    }

	});

});

</script>

<? if (checkPerms('can_post_to_forums')) { ?>

	<div style="width:693px;margin-left:auto;margin-right:auto;">
	
		
		<div style="text-align:right"><a href="#" onclick="opm.showHideDiv('newPostArea'); return false;" class="blueLink">Add Comment</a></div>
		
		<br />
		
		
		
	</div>
	
	<div id="newPostArea" style="display:none;">
	
		<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
				</tr>
				<tr>
					<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
						<div class="fb_content2">
						
							<table border="0" style="margin-left:20px; margin-right:20px;">
							
							<tr>
								<td>
						
									<form name="newpost" action="<?= base_url();?>products/saveForumPost" method="POST">
					
										<input type="hidden" name="opm_productid" value="<?= $product->opm_productid ?>">
										
										<h3 class="forumHeader">Title</h3>
										
										<input type="text" class="forumPostTitle" name="post_title" />
										
										<br />
										
										<h3 class="forumHeader">Post</h3>
										
										<textarea class="forumPost" name="post_text" id="commentPostTxtArea"></textarea>
										
										<br /><br />
										
										<div style="text-align:right">
										<input type="submit" name="submit" value="Save Post">
										</div>
									
									</form>
								</td>
							</tr>
						</table>
						
						
						
						</div>
						
					</td>
				</tr>
				<tr>
					<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
				</tr>
			</table>
		
		</div>

<? } ?>

<? if ($forum->num_rows > 0) { ?>

	<? foreach ($forum->result() as $f) { ?>
	
		<table border="0" cellpadding="0" cellspacing="0" align="center" width="693">
			<tr>
				<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
			</tr>
			<tr>
				<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
					<div class="fb_content2">
					<table border="0" style="margin-left:20px; margin-right:20px;">
						
						<tr>
							<td valign="top"><? $this->opm->displayAvatar($f->userid); ?></td>
							<td>
								<div class="prodSummaryForumHeader">Posted <?=opmDate($f->timestamp)?> by <?= $f->postname ?></div>
								<div class="prodSummaryForumTitle"><?=$f->posttitle?></div>
								<div class="prodSummaryForumPost"><?= nl2br($f->post) ?></div>
							</td>
						</tr>
						
					</table>
					</div>
					
				</td>
			</tr>
			<tr>
				<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
			</tr>
		</table>
	
	<? } ?>
	
<? } else { ?>
	
	<table border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_top.gif" width="693" height="7" border="0" /></td>
			</tr>
			<tr>
				<td background="<?= base_url(); ?>resources/images/fbox_bg.gif" colspan="2">
					<div class="fb_content2">
					<table border="0" style="margin-left:20px; margin-right:20px;">
						
						<tr>
							<td class="prodSummaryForumHeader">No Forum Entries.</td>
						</tr>
						
					</table>
					</div>
					
				</td>
			</tr>
			<tr>
				<td colspan="2"><img src="<?= base_url(); ?>resources/images/fbox_btm.gif" width="693" height="7" border="0" /></td>
			</tr>
		</table>
	

<? } ?>
