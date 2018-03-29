

<form name="userform" action="<?= base_url(); ?>users/save" method="post" enctype="Multipart/Form-Data">
	
	<? if (isset($user->userid)) { ?>
		<input type="hidden" name="userid" value="<?= $user->userid ?>">
	<? } ?>
	<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top">
					
					<h3 class="userField">Username</h3>
					
					<input type="text" name="username" value="<?= (isset($user->username) ? $user->username : null) ?>" class="userField" <? checkDisabled(); ?>/>
					
					<br /><br />
					
					<? if (checkPerms('can_change_navision_customer_code')) { ?>
					
						<h3 class="userField">Navision Vendor Code</h3>
						
						<input type="text" name="nv_customerid" value="<?= (isset($user->nv_customerid) ? $user->nv_customerid : null) ?>" class="userField" <? checkDisabled(); ?>/>
						
						<br /><br />
					
					<? } else { ?>
					
						<input type="hidden" name="nv_customerid" value="<?= $user->nv_customerid ?>" />
					
					<? }?>
					
					<? if (checkPerms('can_change_user_email_addresses') || isset($addMode)) { ?>
					
						<h3 class="userField">Email Address</h3>
					
						
						<input type="text" name="login" value="<?= (isset($user->login) ? $user->login : null) ?>" class="userField" <? checkDisabled(); ?>/>
						
						<br /><br />
					
					<? } else { ?>
					
						<h3 class="userField" style="line-height:1.4em;">Email Address: <?= $user->login ?></h3>
						<input type="hidden" name="login" value="<?= $user->login ?>" />
						<br /><br />
					
					<? } ?>
					
					<? if (1==2) { //(!isset($addMode) && checkPerms('can_change_user_passwords')) {  // only show password if in edit mode ?>
					
						<input type="hidden" name="submitPassword" value="true"> <!-- this tells the form handler to look for passwords! -->
						
						<h3 class="userField">Password</h3>
						
						<? if (checkPerms('can_view_passwords')) { ?>
							<input type="text" name="password" value="<?= (isset($user->password) ? $user->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } else { ?>
							<input type="password" name="password" value="<?= (isset($user->password) ? $user->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } ?>
						
						<br /><br />
						
						<h3 class="userField">Confirm Password</h3>
						
						<? if (checkPerms('can_view_passwords')) { ?>
							<input type="text" name="password2" value="<?= (isset($user->password) ? $user->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } else { ?>
							<input type="password" name="password2" value="<?= (isset($user->password) ? $user->password : null) ?>" class="userField" <? checkDisabled(); ?>/>
						<? } ?>
						<br /><br />
					
					<? } ?>
					
					<h3 class="userField">Address / Info</h3>
					
					<textarea name="address" class="userField" <? checkDisabled(); ?>><?= (isset($user->address) ? $user->address : null) ?></textarea>
					
					<br /><br />
					
					<h3 class="userField">User Is Active</h3>
					
					<input type="checkbox" name="isactive" <? checkDisabled(); ?> <?= (isset($user->isactive) && $user->isactive == 1 ? "CHECKED" : null) ?>>				
	
			
			</td>
			<td valign="top" align="right">
				
				<!-- avatar goes here -->
				
				<table width="25%" cellpadding="0" cellspacing="0" border="0">
				
					<tr>
						<td>
					
							<h3 class="userField">Primary Usergroup</h3>
							
							<select name="usergroupid" class="userField" <? checkDisabled(); ?>>
							
								<option value="0" <? if ($user->usergroupid == 0) echo "SELECTED"; ?>>Please Select...</option>
							
								<? foreach ($usergroups as $key=>$ug) { ?>
								
									<option value="<?= $key ?>" <? if ($user->usergroupid == $key) echo "SELECTED"; ?>><?= $ug ?></option>
								
								<? } ?>
							
							</select>
							
							<br /><br />
							
							<h3 class="userField">Addl Usergroup</h3>
							
							<select name="usergroupid2" class="userField" <? checkDisabled(); ?>>
							
								<option value="0" <? if ($user->usergroupid2 == 0) echo "SELECTED"; ?>>Please Select...</option>
							
								<? foreach ($usergroups as $key=>$ug) { ?>
								
									<option value="<?= $key ?>" <? if ($user->usergroupid2 == $key) echo "SELECTED"; ?>><?= $ug ?></option>
								
								<? } ?>
							
							</select>
							
							<br /><br />
							
							<h3 class="userField">Office</h3>
							
							<select name="officeid" class="userField" <? checkDisabled(); ?>>
							
								<option value="0" <? if ($user->officeid == 0) echo "SELECTED"; ?>>Please Select...</option>
							
								<? foreach ($offices as $key=>$of) { ?>
								
									<option value="<?= $key ?>" <? if ($user->officeid == $key) echo "SELECTED"; ?>><?= $of ?></option>
								
								<? } ?>
							
							</select>
							
							<br /><br />
							
							<? /*<h3 class="userField">Company</h3>
							
							<select name="companyid" class="userField" <? checkDisabled(); ?>>
							
								<option value="0" <? if ($user->companyid == 0) echo "SELECTED"; ?>>Please Select...</option>
							
								<? foreach ($companies->result() as $company) { ?>
								
									<option value="<?= $company->id ?>" <? if ($user->companyid == $company->id) echo "SELECTED"; ?>><?= $company->name ?></option>
								
								<? } ?>
							
							</select>
							
							<br /><br /> */ ?>
						
							<h3 class="userField">Avatar <small>(48x48 pixels)</small></h3>
							
							<? 
								
								if (isset($user->userid)) 
									$this->opm->displayAvatar($user->userid); 
								
							?>
							
							<br /><br />
							
							<input type="file" name="avatar" <? checkDisabled(); ?>/>
							
						</td>
					</tr>
				
				</table>
				
				
				
				<!--<div id="prodSummaryImage"><div id="detailImageDiv"><img src="<?=base_url();?>/imageclass/view/<?=$product->default_imageid?>" width="350" height="350" border="0" id="detailImage"></div></div>-->
				
				
				
			</td>
		</tr>
	</table>
	
	<br />
	
	<table align="right" cellpadding="0" cellspacing="0" border="0">
	
		<? if (!isset($addMode)) {  // edit mode ?>
		
			<tr>
				<td align="right"><input type="submit" value="Save" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } else { ?>
		
			<tr>
				<td align="right"><input type="submit" value="Add User (email will be sent)" <? checkDisabled(); ?>>&nbsp;&nbsp;&nbsp;</td>
			</tr>
		
		<? } ?>
		
		<? if (in_array($this->userinfo->userid, $this->config->item('superAdmins'))) { ?>
		
			<tr>
				<td align="right"><br /><br /><br /><input type="button" onclick="location.href='/users/changeUser/<?=$user->userid?>'" value="Impersonate User">&nbsp;&nbsp;&nbsp;</td>
			</tr>	
		
		<? } ?>
	
	</table>
	

</form>

<br />	
	