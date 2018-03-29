
<? if (!isset($emailSent)) { ?>

<table border="0" cellpadding="4">
	<form method="post" action="<?= base_url() ?>login/forgotPWSubmit">
	<tr>
		<Td colspan="2" class="loginHeader">Please Enter Your Email Address.<br />Your password will be reset and a temporary password will be sent to you via email.<br /><br /></TD>
	</tr>
	<tr>
		<td class="loginText">Email Address:</td>
		<td><input type="text" name="email" class="field_login" value="" /></td>
	</tr>
		<tr>
		<td colspan="2" align="right"><input type="submit" name="login" value="Submit" /></td>
	</tr>
	</form>
</table>

<? } else { ?>

	Your password has been sent to you via email!

<? } ?>