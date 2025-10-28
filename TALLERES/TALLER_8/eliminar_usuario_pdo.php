<?php
require_once "config_pdo.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
  
    $id = $_POST['id'];
    
    $sql = "DELETE FROM usuarios WHERE id = :id";
    
    if($stmt = $pdo->prepare($sql)){
      
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        if($stmt->execute()){
       
            if($stmt->rowCount() > 0) {
                echo "Usuario con ID **$id** eliminado con éxito.";
            } else {
                echo "Advertencia: No se encontró ningún usuario con ID **$id** para eliminar.";
            }
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
<head><title>Eliminar Usuario PDO</title></head>
<body>
    <h3>Eliminar Usuario (PDO)</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div><label>ID a Eliminar</label><input type="number" name="id" required></div>
        <input type="submit" value="Eliminar Usuario">
    </form>
</body>
</html>