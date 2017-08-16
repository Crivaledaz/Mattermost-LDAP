<?php
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Interface de connexion LDAP</title>
	</head>

	<body>
		<form method="post" action="connexion.php">
			<fieldset>
				<legend>Connexion</legend>
				<p>
					<label for="user">Identifiant :</label><input name="user" type="text" id="user" /><br />
					<label for="password">Mot de Passe :</label><input type="password" name="password" id="password" />
				</p>
			</fieldset>

		    <p><input type="submit" value="Connexion" /></p>
		</form>
	</body>
</html>