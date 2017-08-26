<?php
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>LDAP Connection Interface</title>
	</head>

	<body>
		<form method="post" action="connexion.php">
			<fieldset>
				<legend>Connection</legend>
				<p>
					<label for="user">Username :</label><input name="user" type="text" id="user" /><br />
					<label for="password">Password :</label><input type="password" name="password" id="password" />
				</p>
			</fieldset>

		    <p><input type="submit" value="Connect" /></p>
		</form>
	</body>
</html>