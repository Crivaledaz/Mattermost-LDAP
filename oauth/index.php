<?php
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
	    <link rel="stylesheet" type="text/css" href="./style.css">
		<title>LDAP Connection Interface</title>
	</head>

	<body>
		<center>
			<table background="images/login.png" border="0" width="733" height="348" cellspacing="1" cellpadding="4">
				<tr>
					<td width="40%">&nbsp;</td>
					
					<td width="60%">
						<table border="0" width="100%">

							<tr>
								<td align="center">
									<div class="LoginTitle">LDAP Authentification</div>
						

								<form method="post" action="connexion.php">
								
										<table border="0" width="90%" cellpadding="1">
												<tr>
													<td colspan="2" align="left">
														
															<div class="messageLogin" align="center">
																
															</div>												 
														&nbsp;
													</td>
												</tr>
												<tr>
													<td align="left" width="40%" class="LoginUsername">
														Username:&nbsp;
													</td>
													<td width="60%">
														<input type="text" name="user" size="25" value="" >
													</td>
												</tr>
												<tr>
													<td align="left" width="40%" class="LoginUsername">
														Password:&nbsp;
													</td>
													<td width="60%">
														<input type="password" name="password" size="25" value="" >
													</td>
												</tr>
												<tr><td colspan="2">&nbsp;</td></tr>
												<tr>
													<td colspan="2" align="center"> <input type="submit" class="GreenButton" name="login" value="       Connect       " > </td>
												</tr>
												
										    
										</table>
									</form>
									
							</td>
							</tr>
						</table>
					
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>