<?php

if (isset($_GET['show']) && intval($_GET['show']) > 0) {

    $id = intval($_GET['show']);

    // podstrona /articles_list/show/<id>,
    // tutaj wyswietlamy tytul i tresc artykulu, ktorego ID mamy w zmiennej $id
		$stmt = $dbh->prepare("SELECT * FROM articles WHERE id = :id");
		$stmt->execute([':id' => $id]);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			
			print($row['title']);
			print($row['content']);
			
			
		}
		
		print'<a href="/articles_list">Powrót do poprzedniej strony</a> ';
		
			
			
	

} elseif (isset($_GET['edit']) && intval($_GET['edit']) > 0) {

    $id = intval($_GET['edit']);

    if (isset($_POST['title']) && isset($_POST['content'])) {

        // tutaj zapisujemy zmiany w artykule $id, zakladajac, ze w formularzu edycji
        // dla tytulu i tresci nadano atrybuty name="title" oraz name="content",
        // przed zapisem nalezy upewnic sie, ze zalogowany uzytkownik jest autorem artykulu
		
    }
	

	if (isset($_POST['tresc_artykulu'])){
	   $tytul = $_POST['tytul'];
	   $tresc_artykulu = $_POST['tresc_artykulu'];
	   $user_id = $_SESSION['id'];
	   $stmt = $dbh->prepare('UPDATE articles SET title = :title, content = :content WHERE id = :id AND user_id = :user_id');
	   $stmt->execute([':title' => $tytul,':content' => $tresc_artykulu, ':id' => $id, ":user_id" =>$user_id ]);
	   }
	

    // podstrona /articles_list/edit/<id>,
    // tutaj wyswietlamy formularz edycji artykulu, ktorego ID mamy w zmiennej $id
		$stmt = $dbh->prepare("SELECT * FROM articles WHERE id = :id");
		$stmt->execute([':id' => $id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		print'<a href="/articles_list">Powrót do poprzedniej strony</a> ';
		print'
		<form action="/articles_list/edit/' . $id . '" method="POST">
        <input type="text" name="tytul" value = ' . $row['title'] . '>
		<textarea name = "tresc_artykulu" class = "art-editor" style = "height: 300">' . htmlspecialchars($row['content'], ENT_QUOTES | ENT_HTML401, 'UTF-8') . '</textarea>
		<input type="submit" value="Zapisz">
		</form>';
		
} else {

    if (isset($_GET['delete']) && intval($_GET['delete']) > 0) {

        $id = intval($_GET['delete']);
		$user_id = $_SESSION['id'];

        // tutaj usuwamy artykul, ktorego ID mamy w zmiennej $id,
        // przed usunieciem nalezy upewnic sie, ze zalogowany uzytkownik jest autorem artykulu
		$stmt = $dbh->prepare("DELETE FROM articles WHERE id = :id AND user_id = :user_id");
		$stmt->execute([':id' => $id, ':user_id' => $user_id ]);
		
		
    }

    // podstrona /articles_list,
    // tutaj wyswietlamy listę wszystkich artykulow
	$stmt = $dbh->prepare("SELECT * FROM articles ORDER BY id DESC");
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		//print($row['title']); print'</br>';
		//print'<a href="/articles_list/show/' . $row['id'] . '"> '.$row['title'].'</a></br>';
		if(isset($_SESSION['id']) && ($row['user_id'] == $_SESSION['id'])){
			print'<a href="/articles_list/show/' . $row['id'] . '"> '.$row['title'].'</a> 
			<a href="/articles_list/delete/' . $row['id'] . '"><button>Usun</button></a>
			<a href="/articles_list/edit/' . $row['id'] . '"><button>Edytuj</button></a>
			</br>';
			//print($row['title']);
		}
		else{
			print'<a href="/articles_list/show/' . $row['id'] . '"> '.$row['title'].'</a></br>';
		}
		
	}

}

