<?php
require_once 'inc/functions.php';
session_start();
if(!empty($_POST)){

	$errors = array();
	// inclus fichier de connection a la BDD	
	require_once 'inc/db.php';

	// si le champ USERNAME n'a pas été remplie || ou ne correspond pas au critère demander (preg_match)
	if(empty($_POST['username'])  || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['username'])){
		$errors['usermane'] = "Vous pseudo n'est pas valide (alphanumérique)";
	}else{
		$req = $pdo->prepare('SELECT id FROM users WHERE username = ?');
		$req->execute([$_POST['username']]);
		// nous permet de récupere le premier éléments
		$user = $req->fetch();
		if($user){
			$errors['username'] = 'Ce pseudo existe déjà';
		}
	}

	// si le champ EMAIL est vide || ou ne correspond pas une email valide
	if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$errors['email'] = "Votre email n'est pas valide";
	}else{
		$req = $pdo->prepare('SELECT id FROM users WHERE email = ?');
		$req->execute([$_POST['email']]);
		// nous permet de récupere le premier éléments
		$user = $req->fetch();
		if($user){
			$errors['email'] = 'Cet adresse email existe déjà';
		}
	}

	// si le champ password est vide || le password 1 différent de password 2  
	if(empty($_POST['password']) || $_POST['password'] != $_POST['password_confirm']){
		$errors['password'] = "Vous devez rentrer un mot de passe valide";
	}

	if(empty($errors)){
		// Prépare la requête
		$req = $pdo->prepare("INSERT INTO users SET username = ?, email=?, password=?, confirmation_token=?");
		// Hash le MDP
		$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$token = str_random(60);
		// Execute la requête 
		$req->execute([$_POST['username'], $_POST['email'], $password, $token]);
		$user_id = $pdo->LastInsertId();
		mail($_POST['email'], 'Confirmation de votre comte', "Afin de valider votre compte merci de sur ce lien\n\nhttp://localhost/espace_membre/confirm.php?id=$user_id&token=$token");
		$_SESSION['flash']['success'] = "Un email de confirmation vous a été envoyé pour valider votre compte.";
		header('Location: login.php');
		exit();
	}

	debug($errors);

}
?>

<?php require 'inc/header.php'; ?>

<h1 class="text-center"><u>S'inscrire</u></h1>

<?php if(!empty($errors)): ?>
	<div class="alert alert-info">
		<p><strong>Vous n'avez pas rempli le formulaire correctement</strong></p>
		<ul>
			<?php foreach ($errors as $error): ?>
				<li><?= $error; ?></li>
			<?php endforeach; ?>			
		</ul>
	</div>
<?php endif; ?>

&nbsp;
&nbsp;
&nbsp;

<form action="" method="POST">
	<div class="form-group">
		<label for="">Pseudo</label>
		<input type="text" name="username" class="form-control"/>
	</div>
	<div class="form-group">
		<label for="">Email</label>
		<input type="text" name="email" class="form-control"/>
	</div>
	<div class="form-group">
		<label for="">Mot de passe</label>
		<input type="password" name="password" class="form-control"/>
	</div>
	<div class="form-group">
		<label for="">Confirmer votre mot de passe</label>
		<input type="password" name="password_confirm" class="form-control"/>
			&nbsp;
			&nbsp;
	</div>
	<button type="submit" class="btn btn-success">M'inscrire</button>

</form>

<?php require 'inc/footer.php'; ?>
