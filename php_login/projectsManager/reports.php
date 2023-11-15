<?php
    $txtID = (isset($_POST['txtID']))?$_POST['txtID']:"";
    $txtTurno = (isset($_POST['txtTurno']))?$_POST['txtTurno']:"";
    $txtTelefono = (isset($_POST['txtTelefono']))?$_POST['txtTelefono']:"";
    $txtSupervisor = (isset($_POST['txtSupervisor']))?$_POST['txtSupervisor']:"";
    $txtUbicacion = (isset($_POST['txtUbicacion']))?$_POST['txtUbicacion']:"";
    $txtFoto = (isset($_FILES['txtFoto']["name"]))?$_FILES['txtFoto']["name"]:"";
    $txtProblema = (isset($_POST['txtProblema']))?$_POST['txtProblema']:"";

    $accion = (isset($_POST['accion']))?$_POST['accion']:"";

    include("conexion/conexion.php");

    switch($accion){
        case "btnAgregar":
            $sentence = $pdo->prepare("INSERT INTO `anormalreports` (ID, Turno, Telefono, Supervisor, Ubicacion, Foto, Problema) VALUES (:ID,:Turno,:Telefono,:Supervisor,:Ubicacion,:Foto,:Problema)");
            $sentence->bindParam(':ID', $txtID);
            $sentence->bindParam(':Turno', $txtTurno);
            $sentence->bindParam(':Telefono', $txtTelefono);
            $sentence->bindParam(':Supervisor', $txtSupervisor);
            $sentence->bindParam(':Ubicacion', $txtUbicacion);

            //Recepcionaremos la fotografia, la adjuntaremos a la carpeta imagenes y despues la pasaremos a la BD
            $Fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"cieloGuanajuato.jpg";
            
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            $uploads_dir = 'imagenes';

            if($tmpFoto!=""){
                move_uploaded_file($tmpFoto,"$uploads_dir/$nombreArchivo");

            }

            $sentence->bindParam(':Foto', $nombreArchivo);
            $sentence->bindParam(':Problema', $txtProblema);
            
            $sentence->execute();

            // echo $txtID;
            // echo "Presionaste btnAgregar";
        break;
        case "btnModificar":
            $sentence = $pdo->prepare("UPDATE `anormalreports` SET 
            Turno=:Turno, 
            Telefono=:Telefono,
            Supervisor=:Supervisor,
            Ubicacion=:Ubicacion,
            Problema=:Problema WHERE ID=:ID");

            $sentence->bindParam(':Turno', $txtTurno);
            $sentence->bindParam(':Telefono', $txtTelefono);
            $sentence->bindParam(':Supervisor', $txtSupervisor);
            $sentence->bindParam(':Ubicacion', $txtUbicacion);
            // $sentence->bindParam(':Foto', $txtFoto);
            $sentence->bindParam(':Problema', $txtProblema);
            $sentence->bindParam(':ID', $txtID);
            
            $sentence->execute();

            //Recepcionaremos la fotografia, la adjuntaremos a la carpeta imagenes y despues la pasaremos a la BD
            $Fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"cieloGuanajuato.jpg";
            
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            $uploads_dir = 'imagenes';

            if($tmpFoto!=""){
                move_uploaded_file($tmpFoto,"$uploads_dir/$nombreArchivo");

                $sentence = $pdo->prepare("SELECT Foto FROM `anormalreports` WHERE ID=:ID");
                $sentence->bindParam(':ID', $txtID);
                
                $sentence->execute();

                $empleado = $sentence->fetch(PDO::FETCH_LAZY);
                print_r($empleado);

                // se verifica que exista la fotografia dentro de la carpeta imagenes y se elimina
                if(isset($empleado["Foto"])){
                    if(file_exists("imagenes/".$empleado["Foto"])){
                        unlink("imagenes/".$empleado["Foto"]);
                    }
                }

                // actualizacion de la fotografia
                $sentence = $pdo->prepare("UPDATE `anormalreports` SET Foto=:Foto WHERE ID=:ID");
                $sentence->bindParam(':Foto', $nombreArchivo);
                $sentence->bindParam(':ID', $txtID);
                $sentence->execute();

            }

            header('Location: reports.php');

            // echo $txtID;
            // echo "Presionaste btnModificar";
        break;
        case "btnEliminar":
            $sentence = $pdo->prepare("SELECT Foto FROM `anormalreports` WHERE ID=:ID");
            $sentence->bindParam(':ID', $txtID);
            
            $sentence->execute();

            $empleado = $sentence->fetch(PDO::FETCH_LAZY);
            print_r($empleado);

            // se verifica que exista la fotografia dentro de la carpeta imagenes y se elimina
            if(isset($empleado["Foto"])){
                if(file_exists("imagenes/".$empleado["Foto"])){
                    unlink("imagenes/".$empleado["Foto"]);
                }
            }

            $sentence = $pdo->prepare("DELETE FROM `anormalreports` WHERE ID=:ID");
            $sentence->bindParam(':ID', $txtID);
            
            $sentence->execute();

            header('Location: reports.php');

            // echo $txtID;
            // echo "Presionaste btnEliminar";
        break;
        case "btnCancelar":
            // echo $txtID;
            // echo "Presionaste btnCancelar";
        break;
    }

    $sentence = $pdo->prepare("SELECT * FROM `anormalreports` WHERE 1");
    $sentence->execute();
    $listaPersonal = $sentence->fetchAll(PDO::FETCH_ASSOC); //se asocia toda la info. de la BD en un arreglo

    // print_r($listaPersonal); //imprimimos la informacion de la base de datos en un arreglo
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require '../partials/header.php'?>

    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Reportes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- El formulario esta contenido dentro del siguiente modal -->
                        <div class="modal-body">
                            <div class="form-row">
                                <!-- para ocultar el ID hay que comentar la linea siguiente y el type del input cambiarlo a hidden -->
                                <label for="">ID:</label> 
                                <input type="text" name="txtID" required value="<?php echo $txtID;?>" placeholder="" id="txtID" require="">
                                <br>

                                <label for="">Turno:</label>
                                <input type="text" name="txtTurno" required value="<?php echo $txtTurno;?>" placeholder="" id="txtTurno" require="">
                                <br>

                                <label for="">Teléfono:</label>
                                <input type="text" name="txtTelefono" required value="<?php echo $txtTelefono;?>" placeholder="" id="txtTelefono" require="">
                                <br>

                                <label for="">Supervisor:</label>
                                <input type="text" name="txtSupervisor" required value="<?php echo $txtSupervisor;?>" placeholder="" id="txtSupervisor" require="">
                                <br>

                                <label for="">Ubicación:</label>
                                <input type="text" name="txtUbicacion" required value="<?php echo $txtUbicacion;?>" placeholder="" id="txtUbicacion" require="">
                                <br>

                                <label for="">Foto:</label>
                                <input type="file" accept="image/*" name="txtFoto" value="<?php echo $txtFoto;?>" placeholder="" id="txtFoto" require="">
                                <br>

                                <label for="">Problema:</label>
                                <input type="text" name="txtProblema" required value="<?php echo $txtProblema;?>" placeholder="" id="txtProblema" require="">
                                <br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button value="btnAgregar" type="submit" name="accion">Agregar</button>
                            <button value="btnModificar" type="submit" name="accion">Modificar</button>
                            <button value="btnEliminar" type="submit" name="accion">Eliminar</button>
                            <button onclick="location.href=" value="btnCancelar" type="submit" name="accion">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Agregar reporte +
            </button>

        </form>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Turno</th>
                        <th>Telefono</th>
                        <th>Supervisor</th>
                        <th>Ubicacion</th>
                        <th>Problema</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <?php foreach($listaPersonal as $personal) {?>
                    <tr>
                        <td><?php echo $personal['ID']; ?></td>
                        <td><img class="img-thumbnail" width="100px" src="imagenes/<?php echo $personal['Foto']; ?>" /></td>
                        <td><?php echo $personal['Turno']; ?></td>
                        <td><?php echo $personal['Telefono']; ?></td>
                        <td><?php echo $personal['Supervisor']; ?></td>
                        <td><?php echo $personal['Ubicacion']; ?></td>
                        <td><?php echo $personal['Problema']; ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="txtID" value="<?php echo $personal['ID']; ?>">
                                <input type="hidden" name="txtTurno" value="<?php echo $personal['Turno']; ?>">
                                <input type="hidden" name="txtTelefono" value="<?php echo $personal['Telefono']; ?>">
                                <input type="hidden" name="txtSupervisor" value="<?php echo $personal['Supervisor']; ?>">
                                <input type="hidden" name="txtUbicacion" value="<?php echo $personal['Ubicacion']; ?>">
                                <input type="hidden" name="txtFoto" value="<?php echo $personal['Foto']; ?>">
                                <input type="hidden" name="txtProblema" value="<?php echo $personal['Problema']; ?>">

                                <input type="submit" value="Seleccionar" name="accion">
                                <button value="btnEliminar" type="submit" name="accion">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php }?>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>