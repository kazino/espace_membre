<?php
	require 'inc/functions.php';
	logged_only();
	if(!empty($_POST['password'])){
		if($_POST['password'] != $_POST['password_confirm']){
		$_SESSION['flash']['info'] = "Les mots de passes ne correspondent pas";
	}else{
		$user_id = $_SESSION['auth']->id;
		$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
		require_once 'inc/db.php';
		$req = $pdo->prepare('UPDATE users SET password = ?')->execute([$password]);
		$_SESSION['flash']['success'] = "Votre mot de passe a bien été mise a jour";
	}	
}

	require 'inc/header.php'; 
?>

<h1>Bonjour <?= $_SESSION['auth']->username; ?></h1>
	<form action="" method="POST">
		<div class="form-group">
			<input class="form-control" type="password" name="password" placeholder="Changer de mot de passe"/>
		</div>
		<div class="form-group">
			<input class="form-control" type="password" name="password_confirm" placeholder="Confirmation du mot de passe"/>
		</div>
		<button class="btn btn-primary">Changer mon mot de passe</button>
	</form>
	
<?php require 'inc/footer.php'; ?>