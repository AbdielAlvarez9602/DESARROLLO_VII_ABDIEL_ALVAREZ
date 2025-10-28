<?php
require_once "config_pdo.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    
    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        if($stmt->execute()){
            echo "Usuario con ID **$id** actualizado con éxito.";
        } else{
            echo "ERROR: No se pudo ejecutar $sql. " . $stmt->errorInfo()[2];
        }
    }
    
    unset($stmt);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head><title>Actualizar Usuario PDO</title></head>
<body>
    <h3>Actualizar Usuario (PDO)</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div><label>ID a Actualizar</label><input type="number" name="id" required></div>
        <div><label>Nuevo Nombre</label><input type="text" name="nombre" required></div>
        <div><label>Nuevo Email</label><input type="email" name="email" required></div>
        <input type="submit" value="Actualizar Usuario">
    </form>
</body>
</html>