<?php
require_once "config_mysqli.php";

$id = "";
$param_id = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $id = trim($_POST['id']);
    
    $sql = "DELETE FROM usuarios WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){

        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){

            if(mysqli_stmt_affected_rows($stmt) > 0) {
                echo "Usuario con ID **$id** eliminado con éxito.";
            } else {
                echo "Advertencia: No se encontró ningún usuario con ID **$id** para eliminar.";
            }
        } else{
            echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($conn);
        }
    }
    
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head><title>Eliminar Usuario MySQLi</title></head>
<body>
    <h3>Eliminar Usuario (MySQLi)</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div><label>ID a Eliminar</label><input type="number" name="id" required></div>
        <input type="submit" value="Eliminar Usuario">
    </form>
</body>
</html>