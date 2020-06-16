<?php
    session_start();

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
	require __DIR__ . '/vendor/autoload.php';
	define("IN_INDEX", 1);


    include("config.inc.php");

    if (isset($config) && is_array($config)) {

        try {
            $dbh = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4', $config['db_user'], $config['db_password']);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Nie mozna polaczyc sie z baza danych: " . $e->getMessage();
            exit();
        }

    } else {
        exit("Nie znaleziono konfiguracji bazy danych.");
    }

  
	if(isset($_POST['login'])&&isset($_POST['password']))
	{
		  $stmt = $dbh->prepare("SELECT * FROM users WHERE email = :email");
		  $stmt->execute([':email' => $_POST['login']]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);
		  if ($user) {
			  if (password_verify($_POST['password'], $user['password'])) {
				  $_SESSION['id'] = $user['id'];
				  $_SESSION['email'] = $user['email'];
			  }
        }
		
	}
	if(isset($_POST['wyloguj']))
	{
		session_unset();
	}
	
	if (isset($_SESSION['id'])) {
        $stmt = $dbh->prepare("UPDATE users SET last_seen = NOW() WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['id']]);
    }

	
	
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Strona <?php print domena(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>        
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script>tinymce.init({selector:'.art-editor'});</script>
		
		<style>
        html {
            position: relative;
            min-height: 100%;
        }
        body {
            margin-bottom: 60px;
        }
        .footer {
          position: absolute;
          bottom: 0;
          width: 100%;
          height: 60px;
          line-height: 60px;
          background-color: #f5f5f5;
        }
        </style>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>

        <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
          <div class="container">
          <a class="navbar-brand" href="#"><?php print domena(); ?></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav" id="menu-buttons">
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Strona główna</span></a>
              </li>
              <li class="nav-item">
               <!-- <a class="nav-link" href="index.php?page=articles_list">Artykuły</a> -->
			   <a class="nav-link" href="/articles_list">Artykuły</a>
              </li>
			  <?php if(isset($_SESSION['email'])){
				echo'
              <li class="nav-item">
                <a class="nav-link" href="/articles_add">Dodaj artykuł</a>
              </li> ';}?>
              <li class="nav-item">
                <a class="nav-link" href="/register">Rejestracja</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/guest_book">Księga gości</a>
              </li>
            </ul>
          </div>
		  
		 <?php 
		 
			if(isset($_SESSION['email'])){
				//print($_SESSION['email']);
				echo'<div style = "color:white">';print($_SESSION['email']);echo'    </div>';
			echo'
				<form action="" method="POST" class="form-inline my-2 my-lg-0">
				<button class="btn btn-outline-info my-2 my-sm-0" name="wyloguj" type="submit">Wyloguj się</button>
			</form>';
			}
			else
			{	echo'
				<form action="" method="POST" class="form-inline my-2 my-lg-0">
				<input type="text" name="login" class="form-control mr-sm-2" placeholder="Login" aria-label="login" style="width: 150px;">
				<input type="password" name="password" class="form-control mr-sm-2" placeholder="Hasło" aria-label="password" style="width: 150px;">
				<button class="btn btn-outline-info my-2 my-sm-0" type="submit">Zaloguj się</button>
			</form>';
			}
		 
		 ?>
		  
		
        </nav>

        <div class="jumbotron">
            <div class="container">
                <h1 class="display-4">Blog osobisty</h1>
                <p class="lead">Znajdziesz tutaj artykuły na każdy temat.</p>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row">
                <div class="col-md-8">
                <?php
				
					if(!isset($_SESSION['id'])){
						$allowed_pages = ['main', 'articles_list', 'register', 'guest_book'];
						if (isset($_GET['page']) && $_GET['page'] && in_array($_GET['page'], $allowed_pages)) {
							if (file_exists($_GET['page'] . '.php')) {
								include($_GET['page'] . '.php');
							
							} else {
								print 'Plik ' . $_GET['page'] . '.php nie istnieje.';
							
								
							}
						} else {
							include('main.php');
							
						}
					}
					else{
						
						$allowed_pages = ['main', 'articles_list', 'articles_add', 'register', 'guest_book'];
				
						if (isset($_GET['page']) && $_GET['page'] && in_array($_GET['page'], $allowed_pages)) {
							if (file_exists($_GET['page'] . '.php')) {
								include($_GET['page'] . '.php');
							} else {
								print 'Plik ' . $_GET['page'] . '.php nie istnieje.';
								
							}
						} else {
							include('main.php');
							
						}
					}
						
						
					
					
                ?>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div  class="card-header">
                            Użytkownicy online
                        </div>
                        <div id = 'online_users'class="card-body">
                            <p class="card-text"> </p>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            Ostatnie 10 artykułów
                        </div>
                        <div  id= 'articles_live' class="card-body">
                            <p class="card-text"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer mt-auto" style="background-color: #f5f5f5;">
          <div class="container">
            <span class="text-muted">Aktualna data: <?php print date('Y-m-d'); ?></span>
          </div>
        </footer>
		
			
                <script>
                       $.ajax({
                                    url: "https://s54.labwww.pl/api/online",
                                    type: "GET",
                                    dataType : "json",
                                })
								
                                .done(function( data ) {
									
									jQuery.each(data, function() {
										console.log(this.id + " " + this.email);
											var tr = document.createElement('tr')
											//td.append(this.email)
											tr.append(this.email)
											document.getElementById("online_users").appendChild(tr)	
									});
									
								
                                })
                                .fail(function( xhr, status, errorThrown ) {
                                    alert("Nie udalo sie pobrac danych.");
                                });
						$.ajax({
                                    url: "https://s54.labwww.pl/api/articles",
                                    type: "GET",
                                    dataType : "json",
                                })
								
                                .done(function( data ) {
									var tr = document.createElement('tr')
									jQuery.each(data, function() {
										
										
											tr.className = "onlinee"
										    
											
											$(".onlinee").append('<a class ="linkonline" href="/articles_list/show/' + this.id + '">' + this.title + '</a></br>')
											
											
											document.getElementById("articles_live").appendChild(tr)
									});
							
                                })
                                .fail(function( xhr, status, errorThrown ) {
                                    alert("Nie udalo sie pobrac danych.");
                                });   
						
						
    
                    
                </script>

                })
	
    </body>
</html>
