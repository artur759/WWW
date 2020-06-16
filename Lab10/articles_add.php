 <?php	

			if (isset($_POST['tytul'])&&isset($_POST['tresc_artykulu'])){
				   $tytul = $_POST['tytul'];
				   $tresc_artykulu = $_POST['tresc_artykulu'];
				   $user_id = $_SESSION['id'];
				   $stmt = $dbh->prepare('INSERT INTO articles (user_id, title, content, created) VALUES (:user_id, :title, :content, NOW())');
				   $stmt->execute([':user_id' => $user_id, ':title' => $tytul,':content' => $tresc_artykulu]);
			}
?>	


<form action="/articles_add" method="POST">
        <input type="input" name="tytul" placeholder="Tytuł artykułu">
		<input type = "textarea" name = "tresc_artykulu" class = "art-editor" style = "height: 300">
		<input type="submit" value="Dodaj">
</form>
       