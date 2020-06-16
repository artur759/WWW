<?php/* if (!defined('IN_INDEX')) { exit("Nie można uruchomić tego pliku bezpośrednio."); }*/ ?> 

<div class="card mb-3">
	<div class="card-body">
		<?php
			
			if (!defined('IN_INDEX')) { exit("Nie można uruchomić tego pliku bezpośrednio."); }
		
			if (isset($_POST['opinion'])){
				$opinion = $_POST['opinion'];
				$ip = $_SERVER['REMOTE_ADDR'];
				if (mb_strlen($opinion) >= 5 && mb_strlen($opinion) <= 200) {
					
					$recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha_private']);
					$resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                      ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
				
					if ($resp->isSuccess()) {
						$stmt = $dbh->prepare("INSERT INTO guest_book (opinion, ip, created) VALUES (:opinion, :ip, NOW())");
						$stmt->execute([':opinion' => $opinion, ':ip' => $ip]);
						print '<p style="font-weight: bold; color: green;">Dane zostały dodane do bazy.</p>';
					} else {
						$errors = $resp->getErrorCodes();
						
						
					}
				} else {
					print '<p style="font-weight: bold; color: red;">Podane dane są nieprawidłowe.</p>';

					
						
				}
				}
		?>
		<!-- <form action="index.php?page=guest_book" method="POST"> -->
		<form action="/guest_book" method="POST">
        <input type="text" name="opinion" placeholder="Opinia">
        <input type="submit" value="Dodaj">
		<div class="g-recaptcha" data-sitekey= "<?php print($config['recaptcha_public'])?>"></div> 
        </form>

		<table class="table table-striped">
          <thead>
            <tr id="wiersz-naglowka">
              <th scope="col">ID</th>
              <th scope="col">Opinia</th>
			  <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
			<?php 
				if (isset($_GET['delete'])){
					$id = $_GET['delete'];
					$ip = $_SERVER['REMOTE_ADDR'];
					$stmt = $dbh->prepare("DELETE FROM guest_book WHERE id = :id AND ip = :ip");
					$stmt->execute([':id' => $id, ':ip' => $ip]);
				}
			?>
			<?php
				$stmt = $dbh->prepare("SELECT id, opinion, ip, created FROM guest_book");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					
					
					
					if($row['ip'] == $_SERVER['REMOTE_ADDR']){
							print '
						<tr>
						  <td>' . $row['id'] . '</td>
						  <td>' . htmlspecialchars($row['opinion'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
						  <td>' . htmlspecialchars($row['ip'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
						  <td>' . htmlspecialchars($row['created'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
						  <td><a href="/guest_book/delete/' . $row['id'] . '"><button>Usun</button></a></td>
						
						</tr>';
					} else {
							print '
						<tr>
						  <td>' . $row['id'] . '</td>
						  <td>' . htmlspecialchars($row['opinion'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
						  <td>' . htmlspecialchars($row['ip'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
						  <td>' . htmlspecialchars($row['created'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</td>
						  <td></td>
						</tr>';
					}

				}
            ?>
          </tbody>
        </table>
	</div>
</div>



