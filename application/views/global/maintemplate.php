<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<script language="javascript">
			
			var base_url = '<?=base_url();?>';
			
			<? if (isset($product)) { ?>
			var id = '<?= $product->opm_productid ?>';
			<? } ?>
			
			<? if (isset($user)) { ?>
			var id = '<?= $user->userid ?>';
			<? } ?>
			
			<? if (isset($p)) { ?>
			var id = '<?= $p->propertyid ?>';
			<? } ?>
			
		</script>
		
		<script>
		
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		  ga('create', 'UA-57681333-1', 'auto');
		  ga('send', 'pageview');
		
		</script>
			
		<? if (isset($javascripts)) echo $javascripts; ?>
		
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/shadowbox_new.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/shadowbox.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/datepicker.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/autocompleteStyles.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/chosen.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_upload.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/jquery.plupload.queue.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/cloudzoom.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/jquery-ui-1.10.3.custom.min.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_calendar.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/datepicker_jq.css">
		
		<style type="text/css">
		
			BODY {
						
				background-image: url('<?=base_url();?>resources/images/nav_bg.gif');
				background-repeat: repeat-x;
				background-position: 0 83px
			
			}
		
		</style>
		<? if (isset($headInclude)) echo $headInclude; ?>
	</head>
	<body>
		
		<!-- onload="MM_preloadImages('<?=base_url();?>resources/images/nav_products_over.gif','<?=base_url();?>resources/images/nav_properties_over.gif','<?=base_url();?>resources/images/nav_production_over.gif','<?=base_url();?>resources/images/nav_administration_over.gif');"-->
		
		<div id="container">
		
			<div id="header">
				
					<div id="logininfo">
						<table border="0" align="right" width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td align="left" valign="top"><a href="<?= base_url(); ?>"><img src="<?=base_url()?>resources/images/logo.gif" border="0"></a></td>
								<? if (isset($this->userinfo->userid)) { ?>
									<td align="right"><span class="bold">Welcome</span>, <?= $this->userinfo->username ?><br /><?= anchor('login/doLogout', 'log out');?>&nbsp;&nbsp;|&nbsp;&nbsp;<? if (checkPerms('can_edit_preferences')) { ?><?= anchor('mypreferences', 'my prefs');?>&nbsp;&nbsp;|&nbsp;&nbsp;<? } ?><?= anchor('support', 'help');?></td>
									<td width="50"><? $this->opm->displayAvatar($this->userinfo->userid); ?></td>
								<? } ?>
							</tr>	
						</table>
					</div>
				
			
			</div>

			<div id="nav1">
				
				<table border="0" cellpadding="0" cellspacing="0" width="100%" height="30">
					<tr>
						<td align="left" valign="middle">
							<? $this->load->view('global/nav1'); ?>
						</td>
							<form name="quickSearch" method="POST" action="<?=base_url();?>search/submit">
								<td align="right" valign="top">
						
									<? if (checkPerms('can_quicksearch')) { ?>
						
							
										<input type="text" name="searchQuery" value="<?= (isset($isQuickSearch) ? $searchtext : "Quick Product Search") ?>" id="quickprodsearch" onFocus="if (document.quickSearch.searchQuery.value == 'Quick Product Search') document.quickSearch.searchQuery.value = '';" />
										<input type="image" src="<?=base_url();?>resources/images/search_submit.gif" align="absmiddle" style="margin-bottom:4px; margin-left:4px; border-style:none;"></input>
							
						
									<? } ?>
						
							</td>
						</form>
					</tr>
				</table>
				
				
			</div>
			
			<div id="nav2">
				<? if (isset($nav2)) echo $nav2; ?>
			</div>
			
			
			<? if ($this->session->flashdata('alert')) { ?>
					<div id="alertArea"><?= $this->session->flashdata('alert') ?></div>
			<? } ?>
			
			<div id="rightNav"><? if (isset($rightNav)) echo $rightNav; ?></div>
		
			<div id="bigheader"><? if (isset($bigheader)) echo $bigheader; ?></div>
			
			<? if (isset($searchArea)) echo $searchArea; ?>
			
			<? if (isset($contentNav) || isset($content) || isset($contentNav2)) { ?>
			
				<div id="maincontent">
					
					<? if (isset($contentNav)) echo $contentNav; ?>
					
					<br /><br />
					
					<? if (isset($content)) { ?><div id="content"><?= $content ?></div><? } ?>
					
					<? if (isset($contentNav2)) echo "<br><br>".$contentNav2; ?>
					
				</div>
				
			<? } ?>
			
		</div>
		
		<div id="footer">Copyright &copy; 2008 Bravado International Group</div>
		
		<? 
		
		
		if ($this->config->item('debugMode') == true || $this->userinfo->userid == 1 || $_SERVER['REMOTE_ADDR'] == '68.173.125.182') {
		
			if (isset($product)) {
				
				echo "<pre>";
				print_r($product);
				echo "</pre>";
			
			}
			
			if (isset($user)) {
				
				echo "<pre>";
				print_r($user);
				echo "</pre>";
			
			}
			
			if (isset($this->userinfo)) {
				
				echo "userinfo:<pre>";
				print_r($this->userinfo);
				echo "</pre>";
			
			}
		}
		
		
		?>
		
	</body>
</html>