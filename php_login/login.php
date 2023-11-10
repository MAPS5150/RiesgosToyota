<?php
    session_start();

    if(isset($_SESSION['user_id'])) {
        header('Location: /crudtoyota/php_login/');
    }

    require 'database.php';

    if(!empty($_POST['email']) && !empty($_POST['password'])) {
        $records = $conn->prepare('SELECT id, usuario, area, lider, email, password FROM users WHERE email=:email');
        $records->bindParam(':email', $_POST['email']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);

        $message = '';

        if(count($results) > 0 && password_verify($_POST['password'], $results['password'])) {
            $_SESSION['user_id'] = $results['id'];
            header('Location: /crudtoyota/php_login/projectsManager/projects.php');
        } else {
            $message = 'Las contraseñas no coinciden. Vuelve a intentarlo.';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require 'partials/header.php'?>

    <div class="container" style="border: 3px solid black; box-shadow: 0px 0px 20px black;">
        <h1 class="login-txt">Login</h1>
        
        <div class="altacceso">
            <span>o <a href="signup.php" style="text-decoration: none;">Crea una cuenta(SignUp)</a></span>
        </div>
    </div>

    <?php if (!empty($message)) : ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form action="login.php" method="post">
        <input type="text" name="email" placeholder="Introduce tu e-mail">
        <input type="password" name="password" placeholder="Introduce tu contraseña">
        <input type="submit" value="Enviar" style="">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>