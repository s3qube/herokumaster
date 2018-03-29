<table border="0" cellpadding="4">
	<form method="post" action="<?= base_url() ?>login/doLogin">
	<tr>
		<Td colspan="2" class="loginHeader">Please Log In Below <br /><br /></TD>
	</tr>
	<tr>
		<td class="loginText">Username/Email</td>
		<td><input type="text" name="username" class="field_login" value="<? if (isset($cookieInfo->username)) echo $cookieInfo->username; ?>" /></td>
	</tr>
	<tr>
		<td class="loginText">Password</td>
		<td><input type="password" name="password" class="field_login" value="<? if (isset($cookieInfo->password)) echo $cookieInfo->password; ?>" /></td>
	</tr>
	<tr>
		<td class="loginText" colspan="2" align="right"><small>Remember Me</small>&nbsp;&nbsp;<input type="checkbox" name="rememberMe" /></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" name="login" value="Login" /></td>
	</tr>
	
	<tr>
		<td colspan="2" align="right"><a href="<?= base_url() ?>login/forgotPassword" class="forgotLink">I Forgot My Password</a></td>
	</tr>
	</form>
</table>

<script type="text/javascript">

		if (top !== self) top.location.replace(self.location.href);

</script>