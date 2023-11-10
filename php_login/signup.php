<?php
    require 'database.php';

    $message = '';

    if(!empty($_POST['usuario']) && !empty($_POST['area']) && !empty($_POST['lider']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $sql = "INSERT INTO users (usuario, area, lider, email, password) VALUES (:usuario, :area, :lider, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario', $_POST['usuario']);
        $stmt->bindParam(':area', $_POST['area']);
        $stmt->bindParam(':lider', $_POST['lider']);
        $stmt->bindParam(':email', $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password);

        if($stmt->execute()) {
            $message = 'Se creo exitosamente un nuevo usuario.';
        } else {
            $message = 'Hay un error al crear tu usuario. Vuelve a intentaro.';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require 'partials/header.php'?>

    <?php if(!empty($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <div class="container" style="border: 3px solid black; box-shadow: 0px 0px 20px black;">
        <h1 class="signup-txt">Crea una cuenta nueva</h1>
        
        <div class="altacceso">
            <span>o <a href="index.php" style="text-decoration: none;">Accede a tu cuenta</a></span>
        </div>
    </div>

    <form action="signup.php" method="post">
        <input type="text" name="usuario" placeholder="Nombre de usuario">
        <input type="text" name="area" placeholder="Área de trabajo">
        <input type="text" name="lider" placeholder="Lider de grupo">
        <input type="text" name="email" placeholder="E-mail">
        <input type="password" name="password" placeholder="Contraseña">
        <input type="password" name="confirm_password" placeholder="Confirma tu contraseña">
        <input type="submit" value="Enviar">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>