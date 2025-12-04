<?php
require_once "config_pdo.php";
require_once "funciones_log.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        
        $sql = "INSERT INTO usuarios (nombre, email) VALUES (:nombre, :email)";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            
            $stmt->execute();
            echo "Usuario creado con éxito.";
            unset($stmt);
        }
    } catch (PDOException $e) {
        registrarError("Error PDO al crear usuario: " . $e->getMessage());
        echo "Ocurrió un error al intentar crear el usuario. Por favor intente más tarde.";
    }
}
unset($pdo);
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div><label>Nombre</label><input type="text" name="nombre" required></div>
    <div><label>Email</label><input type="email" name="email" required></div>
    <input type="submit" value="Crear Usuario">
</form>
