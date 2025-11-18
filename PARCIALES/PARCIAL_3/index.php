<?php
session_start();
include 'data.php';

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit();
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    foreach ($usuarios as $u) {
        if ($u['usuario'] === $usuario && $u['password'] === $password) {
            session_regenerate_id(true);
            $_SESSION['usuario'] = $u['usuario'];
            $_SESSION['rol'] = $u['rol'];
            $_SESSION['calificacion'] = $u['calificacion'];
            header('Location: dashboard.php');
            exit();
        }
    }
    $error = 'Usuario o contraseña incorrectos';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 640px; margin: 2rem; }
        label { display:block; margin-top: 0.5rem; }
        input[type="text"], input[type="password"] { width:100%; padding:8px; }
        .error { color: #c00; }
    </style>
</head>
<body>

<h2>Inicio de Sesión</h2>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="post" action="">
    <label>Usuario:
        <input type="text" name="usuario" value="<?php echo isset($usuario) ? htmlspecialchars($usuario) : ''; ?>">
    </label>

    <label>Contraseña:
        <input type="password" name="password">
    </label>

    <br>
    <input type="submit" value="Ingresar">
</form>

</body>
</html>
