<?php
require_once "config_mysqli.php";
require_once "funciones_log.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        
        $sql = "INSERT INTO usuarios (nombre, email) VALUES (?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $nombre, $email);
            
            mysqli_stmt_execute($stmt);
            echo "Usuario creado con éxito.";
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        registrarError("Error MySQLi al crear usuario: " . $e->getMessage());
        echo "Ocurrió un error al intentar crear el usuario. Por favor intente más tarde.";
    }
}
mysqli_close($conn);
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div><label>Nombre</label><input type="text" name="nombre" required></div>
    <div><label>Email</label><input type="email" name="email" required></div>
    <input type="submit" value="Crear Usuario">
</form>
