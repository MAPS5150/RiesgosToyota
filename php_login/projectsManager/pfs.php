<?php
    // validacion del envio de los datos de la informacion del formulario
    $txtID = (isset($_POST['txtID']))?$_POST['txtID']:"";
    $txtCondicion = (isset($_POST['txtCondicion']))?$_POST['txtCondicion']:"";
    $txtFoto = (isset($_FILES['txtFoto']["name"]))?$_FILES['txtFoto']["name"]:"";
    $txtRiesgo = (isset($_POST['txtRiesgo']))?$_POST['txtRiesgo']:"";
    $txtNombreTM = (isset($_POST['txtNombreTM']))?$_POST['txtNombreTM']:"";
    $txtCierre = (isset($_POST['txtCierre']))?$_POST['txtCierre']:"";
    $txtOtra = (isset($_POST['txtOtra']))?$_POST['txtOtra']:"";
    // validacion del envio de la informacion del formulario mediante la accion de los botones
    $accion = (isset($_POST['action']))?$_POST['action']:"";

    include("conexion/conexion.php");

    // usamos la condicional switch para activar la accion de cada uno de los botones
    switch($accion){
        case "btnAgregar":
            // insertamos los datos del formulario a la tabla pfsreports
            $sentence = $pdo->prepare("INSERT INTO `pfsreports` (ID, Condicion, Foto, Riesgo, NombreTM, Cierre, Otra) VALUES (:ID,:Condicion,:Foto,:Riesgo,:NombreTM,:Cierre,:Otra)");
            $sentence->bindParam(':ID', $txtID);
            $sentence->bindParam(':Condicion', $txtCondicion);

            //Recepcionaremos la fotografia, la adjuntaremos a la carpeta imagenes y despues la pasaremos a la BD
            $Fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"cieloGuanajuato.jpg";
            
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            $uploads_dir = 'imagenesPFS';

            if($tmpFoto!=""){
                move_uploaded_file($tmpFoto,"$uploads_dir/$nombreArchivo");
            }

            $sentence->bindParam(':Foto', $nombreArchivo);
            $sentence->bindParam(':Riesgo', $txtRiesgo);
            $sentence->bindParam(':NombreTM', $txtNombreTM);
            $sentence->bindParam(':Cierre', $txtCierre);
            $sentence->bindParam(':Otra', $txtOtra);

            $sentence->execute();


            // echo $txtID;
            // echo "Presionaste btnAgregar";
        break;
        case "btnModificar":
            $sentence = $pdo->prepare("UPDATE `pfsreports` SET 
            Condicion=:Condicion, 
            Riesgo=:Riesgo,
            NombreTM=:NombreTM,
            Cierre=:Cierre,
            Otra=:Otra WHERE ID=:ID");

            $sentence->bindParam(':Condicion', $txtCondicion);
            $sentence->bindParam(':Riesgo', $txtRiesgo);
            $sentence->bindParam(':NombreTM', $txtNombreTM);
            $sentence->bindParam(':Cierre', $txtCierre);
            $sentence->bindParam(':Otra', $txtOtra);
            // $sentence->bindParam(':Foto', $txtFoto);
            $sentence->bindParam(':ID', $txtID);

            $sentence->execute();

            //Recepcionaremos la fotografia, la adjuntaremos a la carpeta imagenes y despues la pasaremos a la BD
            $Fecha = new DateTime();
            $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"cieloGuanajuato.jpg";
            
            $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
            $uploads_dir = 'imagenesPFS';

            if($tmpFoto!=""){
                move_uploaded_file($tmpFoto,"$uploads_dir/$nombreArchivo");

                // proceso de eliminado de fotografia
                $sentence = $pdo->prepare("SELECT Foto FROM `pfsreports` WHERE ID=:ID");
                $sentence->bindParam(':ID', $txtID);

                $sentence->execute();

                $empleado = $sentence->fetch(PDO::FETCH_LAZY);

                // se verifica que exista la fotografia dentro de la carpeta imagenes y entonces se elimina
                if(isset($empleado["Foto"])){
                    if(file_exists("imagenesPFS/".$empleado["Foto"])){
                        unlink("imagenesPFS/".$empleado["Foto"]);
                    }
                }

                // actualizacion de la fotografia
                $sentence = $pdo->prepare("UPDATE `pfsreports` SET Foto=:Foto WHERE ID=:ID");
                $sentence->bindParam(':Foto', $nombreArchivo);
                $sentence->bindParam(':ID', $txtID);

                $sentence->execute();
            }

            header('Location: pfs.php');

            // echo $txtID;
            // echo "Presionaste btnModificar";
        break;
        case "btnEliminar":
            $sentence = $pdo->prepare("SELECT Foto FROM `pfsreports` WHERE ID=:ID");
            $sentence->bindParam(':ID', $txtID);

            $sentence->execute();

            $empleado = $sentence->fetch(PDO::FETCH_LAZY);

            // se verifica que exista la fotografia dentro de la carpeta imagenes y se elimina
            if(isset($empleado["Foto"])){
                if(file_exists("imagenesPFS/".$empleado["Foto"])){
                    unlink("imagenesPFS/".$empleado["Foto"]);
                }
            }

            // instruccion de borrado de datos del formulario(menos la fotografia)
            $sentence = $pdo->prepare("DELETE FROM `pfsreports` WHERE ID=:ID");
            $sentence->bindParam(':ID', $txtID);

            $sentence->execute();

            header('Location: pfs.php');

            // echo $txtID;
            // echo "Presionaste btnEliminar";
        break;
        case "btnCancelar":
            // echo $txtID;
            // echo "Presionaste btnCancelar";
        break;
    }
    $sentence = $pdo->prepare("SELECT * FROM `pfsreports` WHERE 1");
    $sentence->execute();
    $listaPFS = $sentence->fetchAll(PDO::FETCH_ASSOC); //se asocia toda la info. de la BD en un arreglo

    // print_r($listaPFS); //imprimimos la informacion de la base de datos en un arreglo
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PFS</title>

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
                                <label for="">ID:</label>
                                <input type="text" name="txtID" required value="<?php echo $txtID;?>" placeholder="" id="txtID" require="">
                                <br>

                                <label for="">Condición:</label>
                                <input type="text" name="txtCondicion" required value="<?php echo $txtCondicion;?>" placeholder="" id="txtCondicion" require="">
                                <br>

                                <label for="">Foto:</label>
                                <input type="file" accept="image/*" name="txtFoto" value="<?php echo $txtFoto;?>" placeholder="" id="txtFoto" require="">
                                <br>

                                <label for="">Riesgo:</label>
                                <input type="text" name="txtRiesgo" required value="<?php echo $txtRiesgo;?>" placeholder="" id="txtRiesgo" require="">
                                <br>

                                <label for="">NombreTM:</label>
                                <input type="text" name="txtNombreTM" required value="<?php echo $txtNombreTM;?>" placeholder="" id="txtNombreTM" require="">
                                <br>

                                <label for="">Cierre:</label>
                                <input type="text" name="txtCierre" required value="<?php echo $txtCierre;?>" placeholder="" id="txtCierre" require="">
                                <br>

                                <label for="">Otra:</label>
                                <input type="text" name="txtOtra" required value="<?php echo $txtOtra;?>" placeholder="" id="txtOtra" require="">
                                <br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button value="btnAgregar" type="submit" name="action">Agregar</button>
                            <button value="btnModificar" type="submit" name="action">Modificar</button>
                            <button value="btnEliminar" type="submit" name="action">Eliminar</button>
                            <button value="btnCancelar" type="submit" name="action">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Agregar condición +
            </button>
            
        </form>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Condicion</th>
                        <th>Riesgo</th>
                        <th>NombreTM</th>
                        <th>Cierre</th>
                        <th>Otra</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <?php foreach($listaPFS as $reportsPFS) {?>
                    <tr>
                        <td><?php echo $reportsPFS['ID']; ?></td>
                        <td><img class="img-thumbnail" width="100px" src="imagenesPFS/<?php echo $reportsPFS['Foto']; ?>" /></td>
                        <td><?php echo $reportsPFS['Condicion']; ?></td>
                        <td><?php echo $reportsPFS['Riesgo']; ?></td>
                        <td><?php echo $reportsPFS['NombreTM']; ?></td>
                        <td><?php echo $reportsPFS['Cierre']; ?></td>
                        <td><?php echo $reportsPFS['Otra']; ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="txtID" value="<?php echo $reportsPFS['ID']; ?>">
                                <input type="hidden" name="txtFoto" value="<?php echo $reportsPFS['Foto']; ?>">
                                <input type="hidden" name="txtCondicion" value="<?php echo $reportsPFS['Condicion']; ?>">
                                <input type="hidden" name="txtRiesgo" value="<?php echo $reportsPFS['Riesgo']; ?>">
                                <input type="hidden" name="txtNombreTM" value="<?php echo $reportsPFS['NombreTM']; ?>">
                                <input type="hidden" name="txtCierre" value="<?php echo $reportsPFS['Cierre']; ?>">
                                <input type="hidden" name="txtOtra" value="<?php echo $reportsPFS['Otra']; ?>">

                                <input type="submit" value="Seleccionar" name="action">
                                <button value="btnEliminar" type="submit" name="action">Eliminar</button>
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