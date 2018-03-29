<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		
		<? if (isset($redirectUrl)) { ?>

			<noscript>
			<meta http-equiv="Refresh" content="2; URL=<?= $redirectUrl?>" />
			</noscript>
		
			<script type="text/javascript">
			
			function exec_refresh()
			{
				window.status = "Redirecting..." + myvar;
				myvar = myvar + " .";
				var timerID = setTimeout("exec_refresh();", 100);
				if (timeout > 0)
				{
					timeout -= 1;
				}
				else
				{
					clearTimeout(timerID);
					window.status = "";
					window.location = "<?= $redirectUrl?>";
				}
			}
			
			var myvar = "";
			var timeout = 20;
			exec_refresh();
			
			</script>
			
		<? } ?>
		
		<title><?=$this->config->item('title_prepend');?> <?= $page_title ?></title>
		<link rel="stylesheet" type="text/css" href="<?=base_url();?>resources/opm_styles.css">
	</head>
	<body>
		
		<div id="alertWrapper">
		
			<img src="<?=base_url();?>resources/images/brav_logo_sm.gif" alt="image" width="78" height="43" />
			
			<div id="alertBox">
			
				<?= $content ?>
				
				<? if (!isset($redirectUrl) && !isset($is_login_page)) { ?>
				
					<br /><br />
					
					Please use your back button to return to the previous page.
				
				<? } ?>
			
			</div>
			
		</div>
	
	</body>
</html>