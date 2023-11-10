<?php
    session_start();

    require 'database.php';

    if(isset($_SESSION['user_id'])) {
        $records = $conn->prepare('SELECT id, email, password FROM users WHERE id=:id');
        $records->bindParam(':id', $_SESSION['user_id']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);

        $user = null;

        if(count($results) > 0) {
            $user = $results;
        }

        // header('Location: /crudtoyota/php_login/');
    }

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
    <title>Bienvenido a la app de Toyota</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require 'partials/header.php'?>

    <?php if(!empty($user)): ?>
        <div class="logout" style="text-align: center;">
            <br>Bienvenid@: <?= $user['email'] ?>
            <br>Accediste exitosamente a tu cuenta.
            <a href="logout.php">Cerrar sesión(LogOut)</a> 
            <br> o dirigete nuevamente a:
            <div>
                <span><a href="projectsManager/reports.php" style="text-decoration: none;">Reportes de anormalidad</a> o <a href="projectsManager/pfs.php" style="text-decoration: none;">Reportes PFS</a></span>
            </div>
        </div>
    <?php else:?>
        <div class="container" style="border: 3px solid black; box-shadow: 0px 0px 20px black;">
            <!-- <h1 style="text-align: center;">Crea una cuenta nueva o accede a tu cuenta</h1> -->
            <div class="altacceso">
                <a href="index.php" style="text-decoration: none;">Acceder </a><span>o </span>
                <!-- <a href="login.php" style="text-decoration: none;">Accede a tu cuenta(LogIn) </a><span>o </span> -->
                <a href="signup.php" style="text-decoration: none;">Crear cuenta</a>
            </div>
            <div style="text-align: center;">
                <image src="https://i.ibb.co/d6DRcsP/felizguanajuato.jpg" alt="Guanajuato" style="width:500px;height:300px;"/>
            </div>
            <div>
                <?php if (!empty($message)) : ?>
                    <p><?= $message ?></p>
                <?php endif; ?>
                <form action="index.php" method="post">
                    <input type="text" name="email" placeholder="Correo electrónico">
                    <input type="password" name="password" placeholder="Contraseña">
                    <input type="submit" value="Iniciar Sesión" style="">
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>