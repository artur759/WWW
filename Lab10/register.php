
 <?php	
 
 
			if (isset($_POST['emailRejestracja'])&&isset($_POST['passwordRejestracja'])){
				 $email = $_POST['emailRejestracja'];
				 $email_sprawdzenie=preg_match('/^[a-zA-Z0-9\-\_\.]+\@[a-zA-Z0-9\-\_\.]+\.[a-zA-Z]{2,5}$/D', $email);
				 if($email_sprawdzenie == 1){
					$password = $_POST['passwordRejestracja'];
					$password_hash = password_hash($password, PASSWORD_DEFAULT);
				
					
					$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_private']);
					$resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                      ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
				
					if ($resp->isSuccess()) {
						  try {
								$stmt = $dbh->prepare('
									INSERT INTO users (
										id, email, password, created
									) VALUES (
										null, :email, :password_hash, NOW()
									)
								');
								$stmt->execute([':email' => $email, ':password_hash' => $password_hash]);
								print '<span style="color: green;">Konto zostało założone.</span>';
							} catch (PDOException $e) {
								print '<span style="color: red;">Podany adres email jest już zajęty.</span>';
							}   
					} else {
						$errors = $resp->getErrorCodes();
					}
 
			}}
			
 
		?>	
		<form action="/register" method="POST">
        <input type="text" name="emailRejestracja" placeholder="E-Mail">
		<br>
		<input type="password" name="passwordRejestracja" placeholder="Hasło">
		<div class="g-recaptcha" data-sitekey=<?php echo $config['recaptcha_public']; ?>></div>
        <input type="submit" value="Załóż konto">
</form>
		